### Travaux
- [ ] AUTHENTIFICATION / [`app/Controllers/Api/AuthController.php`](https://github.com/arbph-dev/codeIgniter/blob/master/app/Controllers/Api/AuthController.php) / AuthController::logout / Revoir et simplifier
- [ ] AUTHENTIFICATION / [`app/Controllers/Api/AuthController.php`](https://github.com/arbph-dev/codeIgniter/blob/master/app/Controllers/Api/AuthController.php) / AuthController::me / À décider lors du nettoyage : TOKEN uniquement


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

- [`POST /api/auth/login`](https://github.com/arbph-dev/codeIgniter/edit/master/doc/AUTH/README.md#authcontrollerlogin) 
- [`POST /api/auth/logout`](https://github.com/arbph-dev/codeIgniter/edit/master/doc/AUTH/README.md#authcontrollerlogout)
- [`GET  /api/auth/me`](https://github.com/arbph-dev/codeIgniter/edit/master/doc/AUTH/README.md#authcontrollerme)
- [`POST /api/auth/register`](https://github.com/arbph-dev/codeIgniter/edit/master/doc/AUTH/README.md#authcontrollerregister)
- [`POST /api/auth/activate`](https://github.com/arbph-dev/codeIgniter/edit/master/doc/AUTH/README.md#authcontrolleractivate)
- [`POST /api/auth/resend`](https://github.com/arbph-dev/codeIgniter/edit/master/doc/AUTH/README.md#authcontrollerresend)

#### controller
[`app/Controllers/Api/AuthController.php`](https://github.com/arbph-dev/codeIgniter/blob/master/app/Controllers/Api/AuthController.php)

##### méthodes

---

###### **AuthController::login**
Cette méthode ne doit **PAS** établir une session navigateur. On vérifie les **credentials** afin de pouvoir ensuite créer un **AccessToken** Shield. Le résultat de `check()` contient le User dans extraInfo().

 
```json
  { "email": "..." , "password": "..." }
```

```php
$authenticator = auth('session')->getAuthenticator();
$result = $authenticator->check($credentials); // vérifie les **credentials**
$user = $result->extraInfo();
$user->generateAccessToken('webapp') //délègue entièrement la création du token à Shield.
```

code http
  - 422 : `$this->validator->getErrors()`
  - 401 : 'Email ou mot de passe invalide'
  - 403 : 'Compte non activé. Vérifiez votre email.'
  - 200 : json avec 'token','email_verified','user' 

---

###### **AuthController::logout**

Le token API doit être révoqué côté serveur.

Shield fournit déjà :
```
     User::revokeAccessToken()
```
 et AccessTokens sait :
```
     Authorization: Bearer <token>
         -> check()
         -> User
         -> AccessToken courant
```

Le code actuel refait donc manuellement une partie de ce travail.
À nettoyer :
```
     $rawToken
     str_replace()
     auth('tokens')->check()
     extraInfo()
```
peuvent probablement être simplifiés en s'appuyant davantage sur l'authentificateur Token de Shield.

---

###### **AuthController::me**
Cette route est destinée à l'authentification TOKEN.

demande à Shield de :
  - lire Authorization: Bearer ...
  - retrouver le token dans identities
  - vérifier expiration
  - vérifier unusedTokenLifetime
  - mettre à jour last_used_at
  - positionner currentAccessToken() sur le User

---


###### **AuthController::register**

```json
  { "shield_username": "..." , "shield_email": "..." , "shield_password": "..." }
```


Le workflow Shield natif est :
```
   RegisterController
       ↓
   création User
       ↓
   groupe par défaut
       ↓
   Events::trigger('register')
       ↓
   startLogin()
       ↓
   startUpAction('register')
       ↓
   ActionController
       ↓
   EmailActivator
```

Ici nous sommes dans une API JSON , nous ne pouvons pas simplement faire la redirection HTML du RegisterController natif.

Nous préparons donc l'action Shield puis appelons son traitement pour déclencher l'envoi du mail d'activation.



---

###### **AuthController::activate**
```json
  { "email": "...", "token": "123456" }
```

ACTIVATION API (sans dépendance à la session Shield)

/auth/a/verify (ActionController) exige l'action "pending" dans la SESSION PHP : après fermeture du navigateur il répond 404.

Ici on retrouve le User par email et on compare le code stocké dans l'identité "email_activate" créée par EmailActivator::createIdentity().


code http
- 200 { message } : compte activé (ou déjà activé)
- 422 { error | errors }   code invalide / validation
- 429 { error }            trop d'essais

---

###### **AuthController::resend**
Régénère un code d'activation et renvoie l'email.

Body JSON : { "email": "..." }

On n'utilise PAS EmailActivator::show() ni startLogin() :
- show() lit le pending user dans la session, et startLogin() lève une LogicException si la session contient déjà un utilisateur.

On appelle donc createIdentity() (publique, supprime l'ancien code) puis on envoie nous-mêmes l'email.

Réponse identique que le compte existe ou non (pas d'énumération).


---

###### **AuthController::sendActivationEmail**

Envoie l'email d'activation (même gabarit que EmailActivator::show()).

---

###### **AuthController::tooManyAttempts**
Limiteur simple basé sur le service Throttler de CodeIgniter (utilise le cache configuré). Retourne true si la limite est dépassée







<!--
######


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
