<?php
//app/Controllers/Api/AuthController.php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
//use App\Models\UserModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Events\Events;


class AuthController extends BaseController
{



    // ─── POST /api/login ─────────────────────────────────────────
/*
Diagnostic
L'erreur vient de AuthController::login() : 
appel auth()->attempt() qui utilise l'authenticator Session par défaut
    il reste des données de session d'une connexion précédente.
    Le auth()->logout() en tête de méthode ne suffit pas 

La vraie solution pour un endpoint API token : ne pas toucher à l'authenticator Session du tout, et vérifier les credentials directement via le UserProvider.

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
                ->setJSON(['errors' => $this->validator->getErrors()]);
        }

        $credentials = [
            'email'    => $this->request->getVar('email'),
            'password' => $this->request->getVar('password'),
        ];

        // ✅ Vérifie les credentials SANS toucher à la session
        /** @var \CodeIgniter\Shield\Authentication\Authenticators\Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();
        $result = $authenticator->check($credentials);

        if (! $result->isOK()) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['error' => 'Email ou mot de passe invalide']);
        }

        // L'utilisateur validé est dans extraInfo()
        $user  = $result->extraInfo();

        // Après check / attempt réussi, $user disponible

        if ($user->isNotActivated()) {
            /* Si attempt() a ouvert une session, la fermer
            if (auth()->loggedIn()) {
                auth()->logout();
            }
            */
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'error'          => 'Compte non activé. Vérifiez votre email.',
                    'email_verified' => false,
                ]);
        }



        $token = $user->generateAccessToken('webapp');


        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'token' => $token->raw_token,
                'email_verified' => true,
                'user'  => [
                    'id'    => $user->id,
                    'username'    => $user->username, //20260508-001 ajout username
                    'email' => $user->email,
                    'groups'      => $user->getGroups(), //20260508-001 ajout groups
                    'permissions' => $user->getPermissions(), //20260508-001 ajout permissions
                ],
            ]);
    }

    // ─── POST /api/register ──────────────────────────────────────
    public function testMail()
    {
        log_message('error', '[testMail] début');

        $email = \Config\Services::email();

        $email->setFrom(
            config('Email')->fromEmail,
            config('Email')->fromName
        );

        $email->setTo('zealot_pm2@outlook.fr');

        $email->setSubject('Test CodeIgniter 4');

        $email->setMessage(
            '<h1>Test</h1><p>Email envoyé directement par CodeIgniter 4.</p>'
        );

        log_message('error', '[testMail] configuration initialisée');

        $result = $email->send(false);

        log_message(
            'error',
            '[testMail] send=' . var_export($result, true)
        );

        if (! $result) {
            log_message(
                'error',
                '[testMail] debugger=' . $email->printDebugger(['headers'])
            );
        }

        return $this->response->setJSON([
            'success'  => $result,
            'debugger' => $result ? null : $email->printDebugger(['headers']),
        ]);
    }




    public function register()
    {
        $rules = [
            'shield_username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'shield_email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'shield_password' => 'required|min_length[8]',

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

        $telFixe    = $this->request->getVar('client_profil_tel') ?: null;
        $telMobile  = $this->request->getVar('client_profil_mobile') ?: null;
        $personneId = $this->request->getVar('client_profil_persid') ?: null;

        $rawOrg = $this->request->getVar('client_profil_orgid');

        $organisationId = (
            $rawOrg !== null &&
            $rawOrg !== '' &&
            (int) $rawOrg > 0
        ) ? (int) $rawOrg : null;

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // ─────────────────────────────────────────────────────────────
            // 1. Création de l'utilisateur Shield
            // ─────────────────────────────────────────────────────────────

            $userModel = model(\CodeIgniter\Shield\Models\UserModel::class);

            $user = new \CodeIgniter\Shield\Entities\User([
                'username' => $username,
                'email'    => $email,
                'password' => $password,
            ]);

            $userModel->save($user);

            // Récupération de l'utilisateur réellement enregistré
            $user = $userModel->findById($userModel->getInsertID());

            // Groupe "user" par défaut
            $userModel->addToDefaultGroup($user);


            // ─────────────────────────────────────────────────────────────
            // 2. Création du profil utilisateur
            // ─────────────────────────────────────────────────────────────

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


            // ─────────────────────────────────────────────────────────────
            // 3. Validation de la transaction
            // ─────────────────────────────────────────────────────────────

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'error' => 'Erreur lors de la création du compte.',
                    ]);
            }


            // ─────────────────────────────────────────────────────────────
            // 4. Action Shield : EmailActivator
            // ─────────────────────────────────────────────────────────────

            $registerAction = setting('Auth.actions')['register'] ?? null;

            if ($registerAction !== null) {

                log_message(
                    'error',
                    '[register] Action Shield register=' . $registerAction
                );

                $authenticator = auth('session')->getAuthenticator();

                $authenticator->startLogin($user);

                log_message(
                    'error',
                    '[register] startLogin OK user_id=' . $user->id
                );

                $hasAction = $authenticator->startUpAction('register', $user);

                log_message(
                    'error',
                    '[register] startUpAction=' . var_export($hasAction, true)
                );

                if ($hasAction) {

                    $actionClass = setting('Auth.actions')['register'];

                    /** @var \CodeIgniter\Shield\Authentication\Actions\ActionInterface $action */
                    $action = \CodeIgniter\Config\Factories::actions($actionClass);

                    log_message(
                        'error',
                        '[register] action=' . get_class($action)
                    );

                    $action->show();

                    log_message(
                        'error',
                        '[register] EmailActivator::show() OK'
                    );

                    return $this->response
                        ->setStatusCode(200)
                        ->setJSON([
                            'message'        => 'Compte créé. Vérifiez votre email pour activer votre compte.',
                            'email_verified' => false,
                        ]);
                }
            }


            // ─────────────────────────────────────────────────────────────
            // 5. Pas d'action EmailActivator :
            //    activation immédiate + token
            // ─────────────────────────────────────────────────────────────

            $user->activate();

            $token = $user->generateAccessToken('webapp');

            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'message'        => 'Compte créé avec succès.',
                    'email_verified' => true,
                    'token'          => $token->raw_token,
                    'user'           => [
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



    // ─── GET /api/profile (protégé ??) ──────────────────────────────

    public function me()
    {
        $user = auth('tokens')->user();
    
        if (!$user && auth()->loggedIn()) {
            $user = auth()->user();
        }
    
        if (!$user) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['error' => 'Non authentifié']);
        }
    
        return $this->response->setStatusCode(200)->setJSON([
            'id'          => $user->id,
            'username'    => $user->username,
            'email'       => $user->email,
            'groups'      => $user->getGroups(),
            'permissions' => $user->getPermissions(),
        ]);
    }


    // ─── GET /api/profile (protégé) ──────────────────────────────
    /*
    public function profile()
    {
        $user = auth('tokens')->user();

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
                'groups'   => $user->getGroups(),
            ]);
    }
    */
    // ─── POST /api/logout (protégé) ──────────────────────────────

    // 2026-05-09-003 : AuthController::logout() — check manuel + kill session Shield
    public function logout()
    {
        $rawToken = $this->request->getHeaderLine('Authorization');
        $rawToken = str_replace('Bearer ', '', trim($rawToken));
    
        if (!empty($rawToken)) {
            $result = auth('tokens')->check(['token' => $rawToken]);
            if ($result->isOK()) {
                $user = $result->extraInfo();
                $user->revokeAccessToken($rawToken);
            }
        }
    
        // Kill la session Shield aussi
        auth()->logout();
    
        return $this->response
            ->setStatusCode(200)
            ->setJSON(['message' => 'Déconnecté avec succès']);
    }            
}