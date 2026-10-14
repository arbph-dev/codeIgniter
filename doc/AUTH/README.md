# Travaux


## Synthèse à réaliser
- [`notes/2026-10-13.md`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-13.md)
- [`notes/2026-10-10.md`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-10.md) * non concerné
- [`notes/2026-10-04.md`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04.md)
- [`notes/2026-10-04-001.md`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-001.md)
- [`notes/2026-10-04-002.md`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-002.md)


## inventaire des fichiers
- [app/Controllers/Api/AuthController.php](https://github.com/arbph-dev/codeIgniter/blob/master/app/Controllers/Api/AuthController.php)
  - https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-001.md#1-authcontrollerphp--m%C3%A9thode-resend
- [`/assets/js/ui/workbench/auth/ToolbarAuthPanel.js`](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/ui/workbench/auth/ToolbarAuthPanel.js)
- [`/assets/js/ui/workbench/core/AuthPanelBase.js`](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/ui/workbench/core/AuthPanelBase.js)
  - [notes/2026-10-04-001.md#4-authpanelbasejs](https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-001.md#4-authpanelbasejs)
- [`app/Config/Routes.php`](https://github.com/arbph-dev/codeIgniter/blob/master/app/Config/Routes.php)

## Backend
API d'authentification de l'application.

**IMPORTANT**
Ce contrôleur ne remplace pas Shield. Il constitue une **façade JSON** pour les besoins de l'API/Web.

Deux mécanismes Shield coexistent volontairement :

1. SESSION
- utilisée par les pages natives Shield :
/login
/register
/logout
- authentificateur : auth('session')
- état conservé dans la session PHP.
 
 2. TOKENS

Le token brut n'est jamais stocké en clair dans la base : Shield en conserve le hash SHA-256.

- utilisé par API :
  - /api/auth/login
  - /api/auth/me
  - /api/auth/logout
- authentificateur : auth('tokens')
- client : Authorization: Bearer <token>
- état stateless côté serveur.

Le token lui-même est entièrement géré par Shield :
```php
User::generateAccessToken()
  -> HasAccessTokens
  -> UserIdentityModel
  -> identities
```

### HISTORIQUE
2026-10-04-002 - register / 
- [`notes/2026-10-04-002.md`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-002.md)



#### routes
authentification :

```php
//services shield
service('auth')->routes($routes);

// grouper sous `/api/auth/`
//── Auth ────────────────────────────────────────────────────────────────────
$routes->group('api/auth', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->post('login',    'AuthController::login');    
    //$routes->get('profile',   'AuthController::profile'); // GET /api/auth/profile
    $routes->get ('me',       'AuthController::me');       // GET  /api/auth/me
    $routes->post('logout',   'AuthController::logout');   // POST /api/auth/logout
    $routes->post('register', 'AuthController::register'); // POST /api/auth/register  ← AJOUT 2026-09-27-002 / 1.2
    $routes->post('activate', 'AuthController::activate');
    $routes->post('resend',   'AuthController::resend');
});
```

#### endpoints 

- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET  /api/auth/me`
- `POST /api/auth/register`
- `POST /api/auth/activate`
- `POST /api/auth/resend`

#### controller
[`app/Controllers/Api/AuthController.php`](https://github.com/arbph-dev/codeIgniter/blob/master/app/Controllers/Api/AuthController.php)

##### méthodes
- AuthController::login
- AuthController::register
- AuthController::activate
- AuthController::resend
- AuthController::sendActivationEmail
- AuthController::tooManyAttempts
- AuthController::me
- AuthController::logout




<!--



[``]()
https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/auth.controller.js
  [notes/2026-10-04-001.md#3-authcontrollerjs](https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-001.md#3-authcontrollerjs)
https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/auth.service.js
  https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-001.md#2-authservicejs

[``]()

### magiklink
porsuivre travail magiklink , mot de passe oublié
se reporter à 
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-002.md#suite
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-001.md#%C3%A0-ne-pas-oublier--le-throttle

les 4 fichiers sont à intégrer


## Register

-->
