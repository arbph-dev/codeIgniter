<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Events\Events;

/**
 * API d'authentification de l'application.
 *
 * IMPORTANT
 * =========
 *
 * Ce contrôleur ne remplace pas Shield.
 * Il constitue une façade JSON pour les besoins de l'API/WebUI.
 *
 * Deux mécanismes Shield coexistent volontairement :
 *
 * 1. SESSION
 *    - utilisée par les pages natives Shield :
 *      /login
 *      /register
 *      /logout
 *    - authentificateur : auth('session')
 *    - état conservé dans la session PHP.
 *
 * 2. TOKENS
 *    - utilisé par notre API :
 *      /api/auth/login
 *      /api/auth/me
 *      /api/auth/logout
 *    - authentificateur : auth('tokens')
 *    - client : Authorization: Bearer <token>
 *    - état stateless côté serveur.
 *
 * Le token lui-même est entièrement géré par Shield :
 *
 *    User::generateAccessToken()
 *        -> HasAccessTokens
 *        -> UserIdentityModel
 *        -> identities
 *
 * Le token brut n'est jamais stocké en clair dans la base :
 * Shield en conserve le hash SHA-256.
 */
class AuthController extends BaseController
{
    /**
     * POST /api/auth/login
     *
     * LOGIN API = TOKEN
     * -----------------
     *
     * Cette méthode ne doit PAS établir une session navigateur.
     *
     * Nous utilisons donc :
     *
     *     auth('session')->getAuthenticator()->check()
     *
     * et NON :
     *
     *     auth()->attempt()
     *
     * Pourquoi ?
     *
     * `attempt()` réalise le login du mécanisme Session.
     * Or ici nous voulons seulement vérifier les credentials
     * afin de pouvoir ensuite créer un AccessToken Shield.
     *
     * Le résultat de `check()` contient le User dans extraInfo().
     *
     * Ensuite :
     *
     *     $user->generateAccessToken('webapp')
     *
     * délègue entièrement la création du token à Shield.
     */
    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]',
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'errors' => $this->validator->getErrors(),
                ]);
        }

        $credentials = [
            'email'    => $this->request->getVar('email'),
            'password' => $this->request->getVar('password'),
        ];

        /*
         * IMPORTANT :
         *
         * `check()` vérifie les credentials mais ne fait pas le
         * login Session.
         *
         * C'est précisément ce que nous voulons pour cette route API.
         *
         * La Session Shield reste donc indépendante du token API.
         */
        /** @var \CodeIgniter\Shield\Authentication\Authenticators\Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        $result = $authenticator->check($credentials);

        if (! $result->isOK()) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'error' => 'Email ou mot de passe invalide',
                ]);
        }

        /*
         * Shield nous retourne le User validé.
         */
        /** @var User $user */
        $user = $result->extraInfo();

        /*
         * Le compte doit être activé avant de délivrer un token API.
         *
         * L'activation elle-même est gérée par le mécanisme Action
         * de Shield lors du processus d'inscription.
         */
        if ($user->isNotActivated()) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'error'          => 'Compte non activé. Vérifiez votre email.',
                    'email_verified' => false,
                ]);
        }

        /*
         * Création du token API.
         *
         * Nous ne créons pas nous-mêmes la ligne `identities`.
         * Shield s'en charge via UserIdentityModel.
         *
         * Le token brut est disponible une seule fois via raw_token
         * et est celui que le client doit conserver.
         */
        $token = $user->generateAccessToken('webapp');

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'token'          => $token->raw_token,
                'email_verified' => true,
                'user'           => [
                    'id'          => $user->id,
                    'username'    => $user->username,
                    'email'       => $user->email,
                    'groups'      => $user->getGroups(),
                    'permissions' => $user->getPermissions(),
                ],
            ]);
    }


    /**
     * POST /api/auth/register
     *
     * INSCRIPTION
     * -----------
     *
     * ATTENTION :
     *
     * Cette méthode est actuellement une implémentation personnalisée
     * de l'inscription Shield.
     *
     * Le contrôleur natif Shield RegisterController fait déjà :
     *
     *   - validation
     *   - création du User
     *   - ajout au groupe par défaut
     *   - événement `register`
     *   - startLogin()
     *   - startUpAction('register')
     *   - traitement de l'Action
     *   - activation
     *   - completeLogin()
     *
     * Notre code ci-dessous reproduit donc une partie de cette mécanique.
     *
     * C'est un point à nettoyer.
     *
     * Objectif futur :
     *
     *     /register
     *         -> Shield natif
     *
     * et ne conserver ici que si nous avons réellement besoin d'une
     * variante API JSON de l'inscription.
     */
    public function register()
    {
        /*
         * TODO NETTOYAGE :
         *
         * Les champs client_profil_* appartiennent à l'ancien système
         * UserProfil.
         *
         * Ils ne font pas partie de Shield.
         *
         * Nous avons décidé de mettre user_profils de côté pour le moment.
         * Cette partie sera donc retirée dans une étape séparée.
         */
        $rules = [
            'shield_username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'shield_email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'shield_password' => 'required|min_length[8]',

            // ANCIEN PROFIL UTILISATEUR : à supprimer lors du nettoyage.
            'client_profil_tel'    => 'permit_empty|max_length[20]',
            'client_profil_mobile' => 'permit_empty|max_length[20]',
            'client_profil_persid' => 'permit_empty|is_natural_no_zero',
            'client_profil_orgid'  => 'permit_empty|is_natural',
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'errors' => $this->validator->getErrors(),
                ]);
        }

        $username = $this->request->getVar('shield_username');
        $email    = $this->request->getVar('shield_email');
        $password = $this->request->getVar('shield_password');

        /*
         * ANCIEN UserProfil :
         * cette partie ne concerne pas Shield et sera supprimée.
         */
        $telFixe     = $this->request->getVar('client_profil_tel') ?: null;
        $telMobile   = $this->request->getVar('client_profil_mobile') ?: null;
        $personneId  = $this->request->getVar('client_profil_persid') ?: null;

        $rawOrg = $this->request->getVar('client_profil_orgid');

        $organisationId = (
            $rawOrg !== null &&
            $rawOrg !== '' &&
            (int) $rawOrg > 0
        ) ? (int) $rawOrg : null;

        $db = \Config\Database::connect();

        $db->transStart();

        try {

            /*
             * 1. Création du User Shield.
             *
             * Cette partie correspond directement au travail effectué
             * par le UserProvider / UserModel de Shield.
             */
            $userModel = model(\CodeIgniter\Shield\Models\UserModel::class);

            $user = new User([
                'username' => $username,
                'email'    => $email,
                'password' => $password,
            ]);

            $userModel->save($user);

            $user = $userModel->findById($userModel->getInsertID());

            /*
             * Shield affecte le groupe par défaut.
             *
             * C'est une mécanique native Shield.
             */
            $userModel->addToDefaultGroup($user);


            /*
             * 2. ANCIEN UserProfil
             *
             * Cette partie n'est pas une mécanique Shield.
             *
             * Elle sera supprimée dans le nettoyage suivant puisque
             * user_profils est actuellement mis de côté.
             */
            $profilModel = model(\App\Models\UserProfilModel::class);

            $existing = $profilModel->findByUserAndOrg(
                (int) $user->id,
                $organisationId
            );

            if ($existing) {
                $db->transRollback();

                return $this->response
                    ->setStatusCode(409)
                    ->setJSON([
                        'error' => 'Un profil existe déjà pour cette organisation.',
                    ]);
            }

            $profilModel->insert([
                'user_id'         => $user->id,
                'tel_fixe'        => $telFixe,
                'tel_mobile'      => $telMobile,
                'personne_id'     => $personneId,
                'adresse_id'      => null,
                'organisation_id' => $organisationId,
                'defaut'          => 1,
            ]);


            /*
             * 3. Validation transaction.
             */
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'error' => 'Erreur lors de la création du compte.',
                    ]);
            }


            /*
             * 4. ACTION REGISTER SHIELD
             *
             * Ici nous reproduisons manuellement une partie du
             * RegisterController natif de Shield.
             *
             * Shield fait normalement :
             *
             *     startLogin($user)
             *     startUpAction('register', $user)
             *
             * puis laisse l'ActionController traiter l'action.
             *
             * Notre code appelle directement $action->show().
             *
             * Cela fonctionne, mais c'est précisément la partie
             * que nous voulons éviter de maintenir nous-mêmes.
             */
            $registerAction = setting('Auth.actions')['register'] ?? null;

            if ($registerAction !== null) {

                $authenticator = auth('session')->getAuthenticator();

                /*
                 * Le register natif Shield démarre ici le pending login.
                 */
                $authenticator->startLogin($user);

                /*
                 * startUpAction() prépare l'action dans l'état
                 * d'authentification Shield.
                 *
                 * Il ne faut pas appeler plusieurs fois cette séquence.
                 */
                $hasAction = $authenticator->startUpAction('register', $user);

                if ($hasAction) {

                    /*
                     * Appel manuel de l'action Shield.
                     *
                     * Le mécanisme natif passe normalement par
                     * ActionController.
                     */
                    $actionClass = setting('Auth.actions')['register'];

                    /** @var \CodeIgniter\Shield\Authentication\Actions\ActionInterface $action */
                    $action = \CodeIgniter\Config\Factories::actions($actionClass);

                    $action->show();

                    return $this->response
                        ->setStatusCode(200)
                        ->setJSON([
                            'message'        => 'Compte créé. Vérifiez votre email pour activer votre compte.',
                            'email_verified' => false,
                        ]);
                }
            }


            /*
             * 5. PAS D'ACTION REGISTER
             *
             * Si aucune action d'activation n'est configurée :
             *
             *     activation immédiate
             *     génération d'un token API
             *
             * Là encore, cette branche est une extension API,
             * pas le workflow Session natif de Shield.
             */
            $user->activate();

            $token = $user->generateAccessToken('webapp');

            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'message'        => 'Compte créé avec succès.',
                    'email_verified' => true,
                    'token'          => $token->raw_token,
                    'user'            => [
                        'id'       => $user->id,
                        'username' => $user->username,
                        'email'    => $user->email,
                        'groups'   => $user->getGroups(),
                    ],
                ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                '[register] ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'error' => 'Erreur serveur lors de l\'inscription.',
                ]);
        }
    }


    /**
     * GET /api/auth/me
     *
     * IDENTITÉ API
     * ------------
     *
     * Cette route est destinée à l'authentification TOKEN.
     *
     *     auth('tokens')->user()
     *
     * demande à Shield de :
     *
     *   - lire Authorization: Bearer ...
     *   - retrouver le token dans identities
     *   - vérifier expiration
     *   - vérifier unusedTokenLifetime
     *   - mettre à jour last_used_at
     *   - positionner currentAccessToken() sur le User
     *
     * Nous ne devons donc pas refaire ces contrôles ici.
     */
    public function me()
    {
        $user = auth('tokens')->user();

        /*
         * FALLBACK SESSION :
         *
         * Ce fallback permet actuellement à /me de fonctionner aussi
         * avec une session Shield.
         *
         * Mais l'endpoint est placé dans /api/auth et notre architecture
         * API repose sur les Bearer tokens.
         *
         * À décider lors du nettoyage :
         *
         *     /api/auth/me = TOKEN uniquement
         *
         * ou conserver volontairement les deux modes.
         */
        if (! $user && auth()->loggedIn()) {
            $user = auth()->user();
        }

        if (! $user) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'error' => 'Non authentifié',
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'id'          => $user->id,
                'username'    => $user->username,
                'email'       => $user->email,
                'groups'      => $user->getGroups(),
                'permissions' => $user->getPermissions(),
            ]);
    }


    /**
     * POST /api/auth/logout
     *
     * LOGOUT API = TOKEN
     * ------------------
     *
     * Le token API doit être révoqué côté serveur.
     *
     * Shield fournit déjà :
     *
     *     User::revokeAccessToken()
     *
     * et AccessTokens sait :
     *
     *     Authorization: Bearer <token>
     *         -> check()
     *         -> User
     *         -> AccessToken courant
     *
     * Le code actuel refait donc manuellement une partie de ce travail.
     *
     * À nettoyer :
     *
     *     $rawToken
     *     str_replace()
     *     auth('tokens')->check()
     *     extraInfo()
     *
     * peuvent probablement être simplifiés en s'appuyant davantage
     * sur l'authentificateur Token de Shield.
     */
    public function logout()
    {
        $rawToken = $this->request->getHeaderLine('Authorization');

        $rawToken = str_replace('Bearer ', '', trim($rawToken));

        if (! empty($rawToken)) {

            /*
             * Shield valide ici :
             *
             * - existence du token
             * - expiration
             * - durée d'inactivité
             *
             * puis retourne le User.
             */
            $result = auth('tokens')->check([
                'token' => $rawToken,
            ]);

            if ($result->isOK()) {

                $user = $result->extraInfo();

                /*
                 * Révocation native Shield.
                 *
                 * User::revokeAccessToken()
                 * -> HasAccessTokens
                 * -> UserIdentityModel
                 * -> suppression du token correspondant.
                 */
                $user->revokeAccessToken($rawToken);
            }
        }

        /*
         * ATTENTION :
         *
         * Ceci déconnecte également la SESSION Shield.
         *
         * Ce n'est normalement pas nécessaire pour un logout API
         * purement Bearer.
         *
         * À décider :
         *
         * - API logout = révocation du token uniquement
         * - Session logout = auth()->logout()
         *
         * Les deux mécanismes sont indépendants.
         */
        auth()->logout();

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'message' => 'Déconnecté avec succès',
            ]);
    }
}
