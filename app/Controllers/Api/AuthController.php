<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Events\Events;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Models\UserIdentityModel;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Traits\Viewable;

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
 * 
 *
 *
 *
 * HISTORIQUE
 * =========
 * 2026-10-04-002 - register / 
 * 
 * 
 */
class AuthController extends BaseController
{
    use Viewable; // nécessaire pour rendre la vue de l'email d'activation (resend)

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
     * INSCRIPTION API
     * ----------------
     *
     * Cette route crée un User Shield et prépare l'action "register".
     *
     * Le workflow Shield natif est :
     *
     *   RegisterController
     *       ↓
     *   création User
     *       ↓
     *   groupe par défaut
     *       ↓
     *   Events::trigger('register')
     *       ↓
     *   startLogin()
     *       ↓
     *   startUpAction('register')
     *       ↓
     *   ActionController
     *       ↓
     *   EmailActivator
     *
     * Ici nous sommes dans une API JSON :
     * nous ne pouvons pas simplement faire la redirection HTML
     * du RegisterController natif.
     *
     * Nous préparons donc l'action Shield puis appelons son traitement
     * pour déclencher l'envoi du mail d'activation.
     *
     * IMPORTANT :
     * user_profils est volontairement absent.
     * Cette partie sera traitée séparément ultérieurement.
     */
    public function register()
    {
        $rules = [
            'shield_username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'shield_email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'shield_password' => 'required|min_length[8]',
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
    
        try {
            // -------------------------------------------------------------
            // 1. Création du User Shield
            // -------------------------------------------------------------
            //
            // Le UserModel reste celui fourni par Shield.
            // Aucune table métier n'intervient dans l'inscription.
            //
            $userModel = model(\CodeIgniter\Shield\Models\UserModel::class);
    
            $user = new \CodeIgniter\Shield\Entities\User([
                'username' => $username,
                'email'    => $email,
                'password' => $password,
            ]);
    
            $userModel->save($user);
    
            // Récupération du User réellement enregistré.
            $user = $userModel->findById($userModel->getInsertID());
    
            if (! $user instanceof \CodeIgniter\Shield\Entities\User) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'error' => 'Impossible de récupérer le compte créé.',
                    ]);
            }
    
            // -------------------------------------------------------------
            // 2. Groupe Shield par défaut
            // -------------------------------------------------------------
            //
            // Même opération que le RegisterController natif Shield.
            //
            $userModel->addToDefaultGroup($user);
    
            // -------------------------------------------------------------
            // 3. Événement Shield "register"
            // -------------------------------------------------------------
            //
            // Le RegisterController natif déclenche cet événement après
            // la création du User et son affectation au groupe par défaut.
            //
            \CodeIgniter\Events\Events::trigger('register', $user);
    
            // -------------------------------------------------------------
            // 4. Action d'inscription / activation
            // -------------------------------------------------------------
            //
            // Shield peut avoir une action "register" configurée.
            //
            // Si elle existe, nous suivons le mécanisme d'authentification
            // Session de Shield pour placer le User en "pending login".
            //
            $registerAction = setting('Auth.actions')['register'] ?? null;
    
            if ($registerAction !== null) {
    
                /** @var \CodeIgniter\Shield\Authentication\Authenticators\Session $authenticator */
                $authenticator = auth('session')->getAuthenticator();
    
                // Même étape que RegisterController::registerAction().
                $authenticator->startLogin($user);
    
                /*
                 * Prépare l'action "register" dans l'état Session Shield.
                 *
                 * IMPORTANT :
                 * startUpAction() ne constitue pas l'envoi du mail.
                 * L'action EmailActivator s'en charge ensuite.
                 */
                $hasAction = $authenticator->startUpAction(
                    'register',
                    $user
                );
    
                if ($hasAction) {
    
                    /*
                     * Le contrôleur natif ferait :
                     *
                     *     return redirect()->route('auth-action-show');
                     *
                     * Dans notre API nous ne voulons pas retourner une page HTML.
                     *
                     * Nous exécutons donc l'action Shield directement afin
                     * de conserver son mécanisme d'envoi d'email.
                     */
                    /** @var \CodeIgniter\Shield\Authentication\Actions\ActionInterface $action */
                    $action = \CodeIgniter\Config\Factories::actions(
                        $registerAction
                    );
    
                    /*
                     * EmailActivator::show() :
                     *
                     * - récupère le pending User ;
                     * - crée l'identité d'activation ;
                     * - génère le code ;
                     * - envoie l'email ;
                     * - retourne normalement la vue d'information.
                     *
                     * Nous ignorons volontairement ici le HTML retourné :
                     * notre contrat est JSON.
                     */
                    $action->show();
    
                    return $this->response
                        ->setStatusCode(201)
                        ->setJSON([
                            'message'        => 'Compte créé. Vérifiez votre email pour activer votre compte.',
                            'email_verified' => false,
                        ]);
                }
            }
    
            // -------------------------------------------------------------
            // 5. Aucun mécanisme d'activation configuré
            // -------------------------------------------------------------
            //
            // Dans ce cas Shield n'attend pas d'action d'activation.
            // Le compte peut être activé directement.
            //
            $user->activate();
    
            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'message'        => 'Compte créé avec succès.',
                    'email_verified' => true,
                    'user'           => [
                        'id'       => $user->id,
                        'username' => $user->username,
                        'email'    => $user->email,
                        'groups'   => $user->getGroups(),
                    ],
                ]);
    
        } catch (\Throwable $e) {
    
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
     * POST /api/auth/activate
     * Body JSON : { "email": "...", "token": "123456" }
     *
     * ACTIVATION API (sans dépendance à la session Shield)
     * ----------------------------------------------------
     *
     * /auth/a/verify (ActionController) exige l'action "pending" dans la
     * SESSION PHP : après fermeture du navigateur il répond 404.
     *
     * Ici on retrouve le User par email et on compare le code stocké dans
     * l'identité "email_activate" créée par EmailActivator::createIdentity().
     *
     *   200 { message }          compte activé (ou déjà activé)
     *   422 { error | errors }   code invalide / validation
     *   429 { error }            trop d'essais
     */
    public function activate()
    {
        $rules = [
            'email' => 'required|valid_email',
            'token' => 'required|exact_length[6]|numeric',
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['errors' => $this->validator->getErrors()]);
        }

        $email = strtolower(trim((string) $this->request->getVar('email')));
        $token = (string) $this->request->getVar('token');

        // Anti force brute : 6 chiffres = 1 000 000 de possibilités
        if ($this->tooManyAttempts('activate:' . $this->request->getIPAddress() . ':' . $email, 5, 60)) {
            return $this->response
                ->setStatusCode(429)
                ->setJSON(['error' => 'Trop de tentatives. Réessayez dans une minute.']);
        }

        // Même réponse si le compte n'existe pas : pas d'énumération d'emails
        $invalid = fn () => $this->response
            ->setStatusCode(422)
            ->setJSON(['error' => 'Code d’activation invalide ou expiré.']);

        /** @var UserModel $userModel */
        $userModel = model(UserModel::class);
        $user      = $userModel->findByCredentials(['email' => $email]);

        if (! $user instanceof User) {
            return $invalid();
        }

        if ($user->isActivated()) {
            return $this->response
                ->setStatusCode(200)
                ->setJSON(['message' => 'Compte déjà activé. Vous pouvez vous connecter.']);
        }

        /** @var UserIdentityModel $identityModel */
        $identityModel = model(UserIdentityModel::class);
        $identity      = $identityModel->getIdentityByType($user, Session::ID_TYPE_EMAIL_ACTIVATE);

        if ($identity === null || ! hash_equals((string) $identity->secret, $token)) {
            return $invalid();
        }

        // Code valide : on supprime l'identité puis on active le compte
        $identityModel->deleteIdentitiesByType($user, Session::ID_TYPE_EMAIL_ACTIVATE);
        $user->activate();

        /*
         * Nettoyage de la session "pending login" laissée par register()
         * (même navigateur, sans rechargement). Sinon les pages natives
         * Shield (/login) redirigeraient vers auth-action-show et
         * recréeraient un code pour un compte déjà actif.
         */
        $authenticator = auth('session')->getAuthenticator();
        if ($authenticator->getPendingUser()?->id === $user->id) {
            session()->remove(setting('Auth.sessionConfig')['field']);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON(['message' => 'Compte activé. Vous pouvez maintenant vous connecter.']);
    }


    /**
     * POST /api/auth/resend
     * Body JSON : { "email": "..." }
     *
     * Régénère un code d'activation et renvoie l'email.
     *
     * On n'utilise PAS EmailActivator::show() ni startLogin() :
     * show() lit le pending user dans la session, et startLogin() lève une
     * LogicException si la session contient déjà un utilisateur.
     * On appelle donc createIdentity() (publique, supprime l'ancien code)
     * puis on envoie nous-mêmes l'email.
     *
     * Réponse identique que le compte existe ou non (pas d'énumération).
     */
    public function resend()
    {
        if (! $this->validate(['email' => 'required|valid_email'])) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['errors' => $this->validator->getErrors()]);
        }

        $email = strtolower(trim((string) $this->request->getVar('email')));

        if ($this->tooManyAttempts('resend:' . $this->request->getIPAddress() . ':' . $email, 3, 300)) {
            return $this->response
                ->setStatusCode(429)
                ->setJSON(['error' => 'Trop de demandes. Réessayez dans quelques minutes.']);
        }

        $generic = fn () => $this->response
            ->setStatusCode(200)
            ->setJSON(['message' => 'Si le compte existe et n’est pas activé, un nouveau code a été envoyé.']);

        /** @var UserModel $userModel */
        $userModel = model(UserModel::class);
        $user      = $userModel->findByCredentials(['email' => $email]);

        if (! $user instanceof User || $user->isActivated()) {
            return $generic();
        }

        $registerAction = setting('Auth.actions')['register'] ?? null;
        if ($registerAction === null || $registerAction === '') {
            return $generic();
        }

        try {
            /** @var \CodeIgniter\Shield\Authentication\Actions\EmailActivator $action */
            $action = \CodeIgniter\Config\Factories::actions($registerAction);
            $code   = $action->createIdentity($user);

            $this->sendActivationEmail($user, $code);
        } catch (\Throwable $e) {
            log_message('error', '[resend] ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Envoi impossible pour le moment.']);
        }

        return $generic();
    }


    /**
     * Envoie l'email d'activation (même gabarit que EmailActivator::show()).
     */
    private function sendActivationEmail(User $user, string $code): void
    {
        helper('email');

        $email = emailer(['mailType' => 'html'])
            ->setFrom(setting('Email.fromEmail'), setting('Email.fromName') ?? '');
        $email->setTo($user->email);
        $email->setSubject(lang('Auth.emailActivateSubject'));
        $email->setMessage($this->view(
            setting('Auth.views')['action_email_activate_email'],
            [
                'code'      => $code,
                'user'      => $user,
                'ipAddress' => $this->request->getIPAddress(),
                'userAgent' => (string) $this->request->getUserAgent(),
                'date'      => Time::now()->toDateTimeString(),
            ],
            ['debug' => false],
        ));

        if ($email->send(false) === false) {
            throw new \RuntimeException(
                'Cannot send activation email for user: ' . $user->email . "\n"
                . $email->printDebugger(['headers'])
            );
        }

        $email->clear();
    }


    /**
     * Limiteur simple basé sur le service Throttler de CodeIgniter
     * (utilise le cache configuré).
     *
     * @return bool true si la limite est dépassée
     */
    private function tooManyAttempts(string $key, int $capacity, int $seconds): bool
    {
        return service('throttler')->check(md5($key), $capacity, $seconds) === false;
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
