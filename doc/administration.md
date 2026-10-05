# transposition de la partie admin (vue - spa)

préparer la transposition de la partie admin : https://zealot.fr/admin

basé sur 
- https://github.com/arbph-dev/codeIgniter/blob/master/app/Controllers/Admin.php
- https://github.com/arbph-dev/codeIgniter/blob/master/app/Views/cms/admin.php
- https://github.com/arbph-dev/codeIgniter/blob/master/app/Views/layouts/cms.php
- https://github.com/arbph-dev/codeIgniter/blob/master/app/Views/cms/components/debug_overlay.php

dans app/Views/cms/admin.php les données sont transmises par app/Controllers/Admin.php 
```
const USERS = <?= json_encode($users ?? [], JSON_UNESCAPED_UNICODE) ?>;
```

dans un premier temps j'ai déjà besoin d'une liste des utilisateurs
	pour une api task a venir
	pour l'administration
	pour les api, exemple messagerie users			

ensuite une liste des utilisateurs avec filtres sur rôles et permissions

dans un second temps il faut exploiter les logs de connexion
 
on va distinguer les travaux front et back end
On aura besoin des ressources
```

use CodeIgniter\Shield\Authentication\Authenticators\Session;  
use CodeIgniter\Shield\Authentication\HMAC\HmacEncrypter;  
use CodeIgniter\Shield\Authentication\Passwords;  
use CodeIgniter\Shield\Entities\AccessToken;  
use CodeIgniter\Shield\Entities\User;  
use CodeIgniter\Shield\Entities\UserIdentity;  
use CodeIgniter\Shield\Exceptions\LogicException;  
use CodeIgniter\Shield\Exceptions\ValidationException;  

---------------------------------------------------------------------
CodeIgniter\Shield\Entities\AccessToken
/vendor/codeigniter4/shield/src/Authentication/Authenticators/AccessTokens.php
https://github.com/arbph-dev/codeIgniter#codeignitershieldentitiesaccesstoken

use CodeIgniter\Shield\Entities\User;
https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/src/Entities/User.php
  
use CodeIgniter\Shield\Entities\UserIdentity;  
https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/src/Entities/UserIdentity.php
```




L’ancien `/admin` construit un DTO utilisateur à partir de Shield, puis le transmet à la vue. 

Le futur admin peut conserver exactement cette logique métier, mais déplacer la récupération des données vers l’API.

### 1. Ce que fait Admin

`Admin.php` impose d’abord :

- utilisateur authentifié ;
- groupe `admin` ou `superadmin` ;
- puis récupère les utilisateurs via le provider Shield avec :
    - `withIdentities()`
    - `withGroups()`
    - `withPermissions()`
    - `findAll(100)`.

Le DTO actuel est :

```
id
username
email
groups[]
permissions[]
active
created_at
last_active
```

C'est donc déjà quasiment le **contrat API** qu'il nous faut.

La vue actuelle ne fait ensuite que filtrer/trier/rendre ce tableau côté navigateur.

---

# 2. trois besoins

### API fonctionnelle

Pour la messagerie, par exemple :

```
GET /api/users
```

Réponse volontairement minimale :
```
{
    "data": [
        {
            "id": 12,
            "username": "arnaud",
            "email": "..."
        }
    ]
}
```

Cette API **ne doit pas exposer les permissions Shield simplement parce que le serveur sait les récupérer**.

---

### API administration

Pour `/admin` :

```
GET /api/admin/users
```

avec le DTO plus riche :

```
{
    "id": 12,
    "username": "arnaud",
    "email": "...",
    "active": true,
    "created_at": "...",
    "last_active": "...",
    "groups": [
        "admin"
    ],
    "permissions": [
        "users.read",
        "users.write"
    ]
}
```

C'est ici que l'on exploite `withIdentities()`, `withGroups()` et `withPermissions()`. Shield fournit précisément ces mécanismes dans `UserModel`.

---

# 3. API 


```
AdminWorkbench
       ▼
AdminUserClient
       ▼
GET /api/admin/users
       ▼
Admin API Controller
       ▼
Shield UserModel
```

Donc :

```
HTML statique
     +
JS Workbench
     +
API
```

---

# 4. Backend à préparer

Je découperais le backend ainsi :

```
app/
├── Controllers/
│   └── Api/
│       └── Admin/
│           └── UsersController.php
│
├── Services/
│   └── Admin/
│       └── AdminUserService.php
│
└── ...
```

Le contrôleur API ne devrait pas contenir toute la logique Shield.

Par exemple :

```
AdminUserService
    ├── list()
    ├── find()
    ├── groups()
    ├── permissions()
    └── loginLogs()
```

Le contrôleur devient alors essentiellement :

```
authentification
        ↓
autorisation admin
        ↓
service
        ↓
réponse JSON
```

---

# 5. Autorisation : point important

 conserver la distinction existante :

```
admin
superadmin
```

L'ancien contrôleur fait :

```
if (!$user->inGroup('admin') && !$user->inGroup('superadmin'))
```

et possède déjà :

```
isSuperAdmin()
```

Mais dans la nouvelle API, faire  la distinction :

```
admin
 ├── users.read
 ├── ...
 │
superadmin
 ├── users.read
 ├── users.write
 ├── security.read
 └── ...
```

Autrement dit, **ne pas faire dépendre toute l'administration uniquement du nom du groupe** à long terme.

Les groupes Shield restent le niveau global ; 
les permissions deviennent le niveau fonctionnel.

---

# 6. Première API : liste utilisateurs

Je commencerais volontairement très petit.

### Endpoint

```
GET /api/admin/users
```

### Version 1

Filtres :

```
q
group
permission
active
```

Par exemple :

```
GET /api/admin/users?q=arnaud
```

```
GET /api/admin/users?group=admin
```

```
GET /api/admin/users?permission=users.read
```

```
GET /api/admin/users?active=1
```

Mais **le filtrage doit être fait côté backend**, pas comme actuellement dans le JS.

L'ancien JS fait actuellement :

```
USERS.filter(...)
```

sur les données déjà téléchargées.

Pour 20 utilisateurs, ce n'est pas grave.

Pour 10 000 utilisateurs, ce n'est plus acceptable.

---

# 7. Pagination

Je la prévoirais dès maintenant dans le contrat, même si nous ne l'exploitons pas immédiatement :

```
GET /api/admin/users?page=1&limit=50
```

Réponse :

```
{
    "data": [],
    "meta": {
        "page": 1,
        "limit": 50,
        "total": 143
    }
}
```

**Attention cependant avec Shield :** il existe actuellement un historique de problème autour de `withIdentities()` + `paginate()`. Le problème a été corrigé dans des évolutions récentes, mais comme ton projet utilise Shield 1.3.0, il faudra tester précisément la version installée avant de bâtir toute la pagination dessus.

Donc je ne ferais pas encore de suppositions sur l'implémentation SQL.

---

# 8. Deuxième niveau : filtres groupes / permissions

L'ancien admin possède déjà les données nécessaires :

```
'groups' => $u->getGroups(),
'permissions' => array_keys($u->getPermissions()),
```

Le futur écran pourrait avoir :

```
Utilisateurs
────────────────────────────────────────

Recherche : [________________________]

Groupe :
[ Tous ▼ ]

Permission :
[ Toutes ▼ ]

Statut :
[ Tous ▼ ]

────────────────────────────────────────

Utilisateur     Email       Groupes     Statut
------------------------------------------------
arnaud          ...         admin       ●
...
```

Puis sélection :

```
┌─────────────────────────────────────────────┐
│ arnaud                                      │
│                                             │
│ ID             12                           │
│ Email          ...                          │
│ Statut         Actif                        │
│ Création       ...                          │
│ Dernière activité ...                       │
│                                             │
│ Groupes                                     │
│ [admin]                                      │
│                                             │
│ Permissions                                 │
│ [users.read] [users.write]                  │
└─────────────────────────────────────────────┘
```

C'est pratiquement le comportement de l'ancien `showDetail()`.

---

# 9. Troisième niveau : logs de connexion

Je séparerais complètement ce chantier.

```
Admin
├── Utilisateurs
│   ├── liste
│   └── détail
│
├── Groupes / permissions
│
└── Sécurité
    └── Connexions
```

Et côté API :

```
GET /api/admin/security/login-logs
```

avec par exemple :

```
user_id
username
date
IP
success
failure_reason
user_agent
```

Puis filtres :

```
utilisateur
IP
succès / échec
date début
date fin
```

Il ne faut pas mélanger cette donnée avec `last_active`.

`last_active` est un **état utilisateur Shield**, tandis que les logs sont un **historique d'événements**.

---

# 10. Les classes Shield que tu cites

Ta liste est pertinente, mais je la répartirais selon leur responsabilité.

### `User`

```
CodeIgniter\Shield\Entities\User
```

Objet utilisateur Shield.

C'est notre objet métier principal côté backend.

### `UserIdentity`

```
CodeIgniter\Shield\Entities\UserIdentity
```

Important pour :

```
email
password
access token
HMAC
...
```

Le `UserModel` utilise justement les identities pour récupérer les informations d'identité.

### `AccessToken`

```
CodeIgniter\Shield\Entities\AccessToken
```

À réserver à la gestion des tokens API.

Il ne faut surtout pas transformer l'admin utilisateur en écran affichant les secrets/token bruts.

Le modèle Shield génère le token brut uniquement au moment de sa création et stocke une forme hachée pour les access tokens.

### `HmacEncrypter`

```
CodeIgniter\Shield\Authentication\HMAC\HmacEncrypter
```

Même logique : **sécurité/API**, pas données générales de l'utilisateur.

### `Passwords`

```
CodeIgniter\Shield\Authentication\Passwords
```

À garder dans le périmètre des opérations de mot de passe :

```
reset
change
validation
```

et surtout pas dans le DTO utilisateur.

### `Session`

```
CodeIgniter\Shield\Authentication\Authenticators\Session
```

Important pour comprendre l'authentification session actuelle, mais l'API d'administration doit continuer à respecter le mécanisme d'authentification API que nous avons déjà mis en place.

La configuration Shield distingue notamment `tokens`, `session` et `hmac`, avec `session` comme authenticator par défaut dans la configuration actuelle.

---

# 11. Debug overlay

Je le traiterais également comme un composant séparé.

L'actuel `debug_overlay.php` affiche :

```
Utilisateur
 ├── id
 ├── username
 ├── active
 └── created_at

Groupes

Permissions

Session & Serveur
 ├── session_id
 ├── PHP
 ├── CI
 └── environment
```

Dans la nouvelle architecture, il ne faut pas faire :

```
Admin API → données utilisateur + données debug + données serveur
```

mais plutôt :

```
GET /api/admin/users/...
GET /api/admin/debug
```

Le debug devient ainsi un **service d'administration indépendant**.

Et évidemment, `/api/admin/debug` doit être réservé au `superadmin`.

---

# 12. Découpage Front / Back que je propose

## BACKEND — étape 1

```
[ ] Admin API authentication
[ ] Admin authorization
[ ] GET /api/admin/users
[ ] DTO utilisateur
[ ] recherche q
[ ] filtre group
[ ] filtre permission
[ ] filtre active
[ ] pagination
```

## FRONTEND — étape 1

```
[ ] AdminWorkbench
[ ] AdminUserListPanel
[ ] AdminUserDetailPanel
[ ] AdminUserClient
[ ] recherche
[ ] filtres
[ ] tri
[ ] sélection utilisateur
```

On peut reprendre directement les conventions de nos workbenches :

```
assets/js/ui/workbench/admin/
    AdminWorkbench.js
    AdminUserListPanel.js
    AdminUserDetailPanel.js
```

et éventuellement :

```
assets/js/api/
    AdminUserClient.js
```

---

# 13. Puis étape 2

### Backend

```
[ ] GET /api/admin/groups
[ ] GET /api/admin/permissions
[ ] GET /api/admin/users?group=
[ ] GET /api/admin/users?permission=
[ ] GET /api/admin/security/login-logs
```

### Frontend

```
[ ] filtre groupes
[ ] filtre permissions
[ ] panneau sécurité
[ ] historique connexions
[ ] filtres temporels
```

---

# 14. Enfin étape 3 : administration réelle

Je garderais volontairement cela pour après :

```
création utilisateur
activation/désactivation
groupes
permissions
reset mot de passe
révocation tokens
gestion profils
```

Parce que **lister un utilisateur n'est pas administrer un utilisateur**.

Cela nous permettra d'avoir d'abord une API de consultation stable avant d'autoriser les mutations.

---

## Architecture cible

Je vois donc quelque chose comme :

```
                         ┌─────────────────────┐
                         │      Admin UI        │
                         │     /admin           │
                         └──────────┬──────────┘
                                    │
                              AdminWorkbench
                                    │
                 ┌──────────────────┴─────────────────┐
                 │                                    │
          AdminUserClient                     AdminSecurityClient
                 │                                    │
                 ▼                                    ▼
       /api/admin/users                    /api/admin/security/*
                 │                                    │
                 └──────────────────┬─────────────────┘
                                    ▼
                         Admin API Controllers
                                    │
                         Admin Services Layer
                                    │
              ┌─────────────────────┴────────────────────┐
              │                                          │
        Shield UserModel                         Security / Logs
              │
       ┌──────┼─────────┐
       ▼      ▼         ▼
    User  Identity   Groups/Permissions
```

**C'est cette structure que je prendrais comme base de transposition.**

Et surtout, je ne toucherais pas encore au front actuel de `ui.html`/`uiapp.js`. La première tâche propre est de **définir et implémenter le contrat `GET /api/admin/users`**, en réutilisant le DTO que ton ancien `Admin.php` produit déjà. Ensuite seulement on monte le `AdminWorkbench`.




