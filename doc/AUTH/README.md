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

# Backend
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

## HISTORIQUE
2026-10-04-002 - register / 
- [`notes/2026-10-04-002.md`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-002.md)



## routes
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

### endpoints 

- [`POST /api/auth/login`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/AUTH/README.md#authcontrollerlogin) 
- [`POST /api/auth/logout`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/AUTH/README.md#authcontrollerlogout)
- [`GET  /api/auth/me`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/AUTH/README.md#authcontrollerme)
- [`POST /api/auth/register`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/AUTH/README.md#authcontrollerregister)
- [`POST /api/auth/activate`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/AUTH/README.md#authcontrolleractivate)
- [`POST /api/auth/resend`](https://github.com/arbph-dev/codeIgniter/blob/master/doc/AUTH/README.md#authcontrollerresend)

## controller
[`app/Controllers/Api/AuthController.php`](https://github.com/arbph-dev/codeIgniter/blob/master/app/Controllers/Api/AuthController.php)

### méthodes

---

#### **AuthController::login**
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

#### **AuthController::logout**

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

#### **AuthController::me**
Cette route est destinée à l'authentification TOKEN.

demande à Shield de :
  - lire Authorization: Bearer ...
  - retrouver le token dans identities
  - vérifier expiration
  - vérifier unusedTokenLifetime
  - mettre à jour last_used_at
  - positionner currentAccessToken() sur le User

---


#### **AuthController::register**

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

code http
- 422 : `$this->validator->getErrors()`
- 500 : 'Impossible de récupérer le compte créé.'
- 201 : 'Compte créé. Vérifiez votre email pour activer votre compte.'
- 201 : 'Compte créé avec succès.' (sans email configuré)
- 500 : 'Erreur serveur lors de l\'inscription.'
---

#### **AuthController::activate**
```json
  { "email": "...", "token": "123456" }
```

ACTIVATION API (sans dépendance à la session Shield)

/auth/a/verify (ActionController) exige l'action "pending" dans la SESSION PHP : après fermeture du navigateur il répond 404.

Ici on retrouve le User par email et on compare le code stocké dans l'identité "email_activate" créée par EmailActivator::createIdentity().


code http
- 200 : { message } : 'Compte déjà activé. Vous pouvez vous connecter.'
- 200 : 'Compte activé. Vous pouvez maintenant vous connecter.'
- 422 : { error | errors }   code invalide / validation
- 422 : 'Code d’activation invalide ou expiré.'
- 429 : { error }            trop d'essais

---

#### **AuthController::resend**
Régénère un code d'activation et renvoie l'email.

```json
  { "email": "..."}
```

On n'utilise PAS EmailActivator::show() ni startLogin() :
- show() lit le pending user dans la session, et startLogin() lève une LogicException si la session contient déjà un utilisateur.

On appelle donc createIdentity() (publique, supprime l'ancien code) puis on envoie nous-mêmes l'email.

Réponse identique que le compte existe ou non (pas d'énumération).

code http
- 422 : { error | errors }   code invalide / validation
- 429 : { error } 'Trop de demandes. Réessayez dans quelques minutes.'
- 500 : { error } 'Envoi impossible pour le moment.'
- 200 : { message } : 'Si le compte existe et n’est pas activé, un nouveau code a été envoyé.'
---

#### **AuthController::sendActivationEmail**

Envoie l'email d'activation (même gabarit que EmailActivator::show()).

---

#### **AuthController::tooManyAttempts**
Limiteur simple basé sur le service Throttler de CodeIgniter (utilise le cache configuré). Retourne true si la limite est dépassée


# Frontend

## Fichiers
- [auth.controller.js](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js)
  - import : fetchActivate , fetchLogin , fetchLogout , fetchMe , fetchRegister , fetchResend from './auth.service.js'
  - import : authStore from './auth.store.js'

- [auth.service.js](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.service.js)
  - export : fetchActivate , fetchLogin , fetchLogout , fetchMe , fetchRegister , fetchResend

- [auth.store.js](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.store.js)
  - export : authStore



## Bus events
- auth:activate
- auth:activated
- auth:changed
- auth:check
- auth:error
- auth:guest
- auth:loading
- auth:login
- auth:logout
- auth:register
- auth:register:pending
- auth:resend
- auth:success

<!-- ========================================================================================================================================== -->
---

### auth:activate
POST /api/auth/activate { email, token }

#### publisher

#### subscriber
- [bus.subscribe('auth:activate', async ({ token })](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L138)
  - fetchActivate({ email: authStore.pendingEmail, token }) L148

<!-- ========================================================================================================================================== -->

---

### auth:activated
compte activé (aucun token créé, l'utilisateur doit se connecter)

#### publisher
- bus.subscribe('auth:activate', async ({ token })
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L151)

#### subscriber

<!-- ========================================================================================================================================== -->

---

### auth:changed
login / register avec connexion immédiate / logout

#### publisher
- bus.subscribe('auth:login', async ({ email, password }) L58
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L73)
- bus.subscribe('auth:register', async (payload) L92
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L117)
- bus.subscribe('auth:logout', async () L192
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L207)

#### subscriber


<!-- ========================================================================================================================================== -->

---

### auth:check              
vérifie si un token valide existe (GET /api/auth/me)

#### publisher
- [`/assets/js/app-index.js`](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/app-index.js#L83)

#### subscriber
- [bus.subscribe('auth:check', async ()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L31)
  - fetchMe(authStore.token) L37

#### notes
- https://github.com/arbph-dev/codeIgniter/blob/01ea54acea2c0d02df81bb8828f98fabe44a0cbb/doc/notes/2026-10-04-002.md?plain=1#L142
- https://github.com/arbph-dev/codeIgniter/blob/01ea54acea2c0d02df81bb8828f98fabe44a0cbb/doc/notes/2026-10-03-001.md?plain=1#L24
- https://github.com/arbph-dev/codeIgniter/blob/01ea54acea2c0d02df81bb8828f98fabe44a0cbb/doc/notes/2026-10-03-001.md?plain=1#L128

<!-- ========================================================================================================================================== -->

---
###  auth:error

#### publisher
- bus.subscribe('auth:login', async ({ email, password }) L58
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L82)
- bus.subscribe('auth:register', async (payload) L92
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L128)
- bus.subscribe('auth:activate', async ({ token }) L138
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L155)
- bus.subscribe('auth:resend', async () L165
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L183)

#### subscriber

<!-- ========================================================================================================================================== -->

---
### auth:guest

#### publisher
- bus.subscribe('auth:check', async () L31
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L46)
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L50)
- bus.subscribe('auth:logout', async () L192
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L208)

#### subscriber


<!-- ========================================================================================================================================== -->
---

### auth:loading
**payload**
```js
  bool // (true,false)
```
#### publisher
- bus.subscribe('auth:check', async () L31
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L34)
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L53)
- bus.subscribe('auth:login', async ({ email, password }) L58
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L61)
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L86)
- bus.subscribe('auth:register', async (payload)
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L95)
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L131)
- bus.subscribe('auth:activate', async ({ token })
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L141)
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L159)
- bus.subscribe('auth:resend', async () L165
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L168) => true
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L187) => false
- bus.subscribe('auth:logout', async () L192
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L194) => true
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L206) => false

#### subscriber



<!-- ========================================================================================================================================== -->

---

### auth:login
seul endroit qui récupère le Personal Access Token

#### publisher

#### subscriber
- [bus.subscribe('auth:login', async ({ email, password }](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L58)
  - fetchLogin({ email, password }) L64

<!-- ========================================================================================================================================== -->

---

### auth:logout

#### publisher

#### subscriber
- [bus.subscribe('auth:logout', async ()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L192)
  - fetchLogout(authStore.token) L198

<!-- ========================================================================================================================================== -->

---

### auth:register
crée le compte

#### publisher

#### subscriber
- [bus.subscribe('auth:register', async (payload)](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L92)
  - fetchRegister(payload) L98
<!-- ========================================================================================================================================== -->

---

### auth:register:pending
compte non activé : afficher message + saisie du code

#### publisher
- [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L79)
- [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L103)
- [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L122)
- bus.subscribe('auth:resend', async () L165
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L177)

#### subscriber



<!-- ========================================================================================================================================== -->

---

### auth:resend
POST /api/auth/resend { email } → nouveau code par email

#### publisher

#### subscriber
- [bus.subscribe('auth:resend', async ()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L165)
  - fetchResend(authStore.pendingEmail) L175

<!-- ========================================================================================================================================== -->

---
### auth:success
l'application dispose d'une authentification exploitable

**payload**
```js
{ user, token}
```

#### publisher
- bus.subscribe('auth:check', async ()
  - [initAuthController() ](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L43)
- bus.subscribe('auth:login', async ({ email, password })
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L72)
- bus.subscribe('auth:register', async (payload)
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L116)

#### subscriber
- [bus.subscribe('auth:success', ({ user })](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L213)


<!-- ========================================================================================================================================== -->

## revoir
authStore.pendingEmail
- email du compte en attente d'activation. Renseigné par auth:register et par auth:login (403 non activé), conservé en mémoire seulement (jamais le mot de passe).

Dans [initAuthController() ](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L102)
```js
authStore.pendingEmail = payload.shield_email
```

<!-- ========================================================================================================================================== -->

## Service

- fetchActivate({ email: authStore.pendingEmail, token }) 
  - [initAuthController()](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/auth/auth.controller.js#L148)
    

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
