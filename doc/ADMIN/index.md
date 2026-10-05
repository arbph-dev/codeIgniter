# Architecture backend que je retiendrais
la récupération d'un utilisateur Shield est commune.
```
app/
├── Controllers/
│   └── Api/
│       ├── Admin/
│       │   └── UsersController.php
│       │
│       └── Team/
│           └── UsersController.php
│
├── Services/
│   └── Users/
│       └── UserService.php
│
└── Authorization/
    └── ...
```

---

# 2. Différence Admin / Team

C'est là que l'architecture devient intéressante.

## Admin

```
GET /api/admin/users
```

Accès administration.

Filtres :

```
q
group
permission
active
```

Réponse riche :

```
{
    "id": 12,
    "username": "arnaud",
    "email": "...",
    "active": true,
    "groups": ["admin"],
    "permissions": [
        "tasks.view",
        "tasks.manage"
    ],
    "created_at": "...",
    "last_active": "..."
}
```

---

## Team

```
GET /api/team/users
```

Accès aux utilisateurs que l'utilisateur courant est autorisé à voir dans son contexte Team.

Filtres :

```
q
group
permission
active
```

mais **les filtres disponibles et les données retournées doivent être contrôlés par le backend**.

Par exemple, je ne donnerais pas automatiquement à Team :

```
created_at
last_active
toutes les permissions
```

si ce n'est pas nécessaire.

Cela prépare notamment les usages :

```
RelationPickerDialog
    ↓
recherche utilisateurs
    ↓
sélection responsable
    ↓
project_members
```

---

# 3. Le service commun

Je verrais :

```
UserService
├── list()
├── find()
├── search()
├── groups()
├── permissions()
└── ...
```

Mais `list()` et `search()` peuvent en réalité être une même mécanique.

Par exemple :

```
$list = $userService->list([
    'q'          => $q,
    'group'      => $group,
    'permission' => $permission,
    'active'     => $active,
    'page'       => $page,
    'per_page'   => $perPage,
]);
```

Le contrôleur Admin appelle le service avec un **scope admin**.

Le contrôleur Team avec un **scope team**.

---

# 4. Je séparerais aussi `find()` de `list()`

Oui à :

```
list()
find()
```

car les deux réponses n'ont pas forcément la même richesse.

### Liste

```
id
username
email
active
groups éventuellement
```

### Détail

```
id
username
email
active
groups
permissions
created_at
last_active
profil
```

Cela évite de faire une requête lourde pour chaque ligne d'un tableau.

---

# 5. Les routes

Je préparerais explicitement :

```
GET /api/admin/users
GET /api/admin/users/{id}

GET /api/team/users
GET /api/team/users/{id}
```

Puis, conformément à la guideline existante :

```
GET /api/admin/users/like
GET /api/team/users/like
```

pour les composants d'autocomplete.

Et plus tard :

```
GET /api/admin/users/batch?ids=1,2,3
GET /api/team/users/batch?ids=1,2,3
```

La guideline `2026-09-26-002` prévoit précisément `/like`, `/batch`, `/:id`, `q`, `len`, `page`, `per_page`, `fields`, etc.

**Mais je ne développerais pas `/like` et `/batch` maintenant** si la priorité immédiate est la liste + recherche. On réserve les routes.

---

# 6. Attention à `limit` vs `per_page`

Dans ton brouillon tu proposes :

```
?page=1&limit=50
```

mais ta guideline existante définit :

```
?page=2&per_page=20
```

Je garderais **`per_page`**.

Donc :

```
GET /api/admin/users?page=1&per_page=50
```

et :

```
GET /api/team/users?page=1&per_page=50
```

Cela évite d'avoir deux conventions API dans le projet.

---

# 7. Recherche

Pour les deux :

```
GET /api/admin/users?q=arnaud
GET /api/team/users?q=arnaud
```

Le `q` devrait rechercher au minimum :

```
username
email
```

et éventuellement, plus tard :

```
personne.nom
personne.prenom
```

Mais **pas encore**, puisque la liaison `user → personne` fait partie des travaux que tu as identifiés.

C'est cohérent avec ton chantier `user_profils` / `personne` / `organisation`.

---

# 8. Groupes et permissions

Pour Shield :

```
$u->getGroups()
```

et :

```
array_keys($u->getPermissions())
```

restent la bonne source.

Mais je ferais une distinction importante dans l'API :

```
group
```

est un filtre sur les groupes Shield.

```
permission
```

est un filtre sur les permissions effectives de l'utilisateur.

Donc :

```
GET /api/admin/users?group=TaskEditor
```

et :

```
GET /api/admin/users?permission=tasks.edit
```

ne signifient pas la même chose.

C'est particulièrement important pour ton modèle de permissions granulaires.

Ton document `2026-09-26-006` confirme justement l'objectif : Shield pour le droit global, puis contrôle contextuel en base pour les droits dépendant du projet ou de la tâche.

---

# 9. Et cela prépare correctement `tasks`

Ton modèle devient :

```
Shield
│
├── Group
│   └── TaskEditor
│
└── Permission
    ├── tasks.view
    ├── tasks.create
    ├── tasks.edit
    └── tasks.manage
```

Puis :

```
ProjectMember
│
├── project_id
├── user_id
└── role
```

Puis :

```
Task
│
├── project_id
└── created_by
```

Donc :

```
User
 │
 ├── Shield groups
 ├── Shield permissions
 │
 └── ProjectMember
       │
       └── Project
             │
             └── Task
```

C'est exactement la séparation RBAC / ABAC que tu as définie dans `2026-09-26-006`.

---

# 10. Frontend : je ne mettrais PAS encore Workbench

Sur ce point, je suis plutôt d'accord avec ton intuition :

> peut-être d'abord features plus flexibles

**Oui.**

Puisque le premier besoin est simplement :

```
panel-card
 ├── recherche
 ├── filtres
 └── liste utilisateurs
```

je ne créerais pas immédiatement :

```
AdminWorkbench
AdminUserListPanel
AdminUserDetailPanel
```

Le Workbench deviendrait une couche supplémentaire avant qu'elle soit nécessaire.

---

# 11. Je partirais plutôt sur une Feature Users

```
assets/js/features/users/

    users.controller.js
    users.service.js
    users.store.js
    users.renderer.js
    users.form.js
    index.js
```

Puis éventuellement :

```
assets/js/features/admin/
    admin.controller.js
```

qui orchestre la fonctionnalité Users dans le contexte Admin.

Et :

```
assets/js/features/team/
    team.controller.js
```

pour le contexte Team.

On obtient :

```
                    Users Feature
                         │
            ┌────────────┴────────────┐
            │                         │
        Admin                    Team
            │                         │
 admin.controller.js        team.controller.js
            │                         │
            └────────────┬────────────┘
                         │
                    users.service
                         │
                    users.store
                         │
                  users.renderer
```

C'est beaucoup plus réutilisable.

---

# 12. Et `RelationPickerDialog`

C'est justement là que cette approche devient payante.

Plus tard :

```
RelationPickerDialog
        │
        ▼
UsersService.search()
        │
        ▼
GET /api/team/users?q=...
```

Le picker ne saura même pas si les utilisateurs viennent de :

```
Admin
Team
Project
Task
```

Il demande simplement :

```
usersService.search("arnaud")
```

C'est le bon niveau d'abstraction.

---

# 13. `domHelper`

Oui, systématiquement.

Par exemple :

```
import {
    byId,
    qs,
    qsa,
    create
} from '/assets/js/core/domhelper.js'
```

Le renderer ne doit pas faire du gros HTML injecté avec des chaînes partout.

Il peut produire :

```
users.renderer
    ├── renderList()
    ├── renderRow()
    ├── renderEmpty()
    ├── renderLoading()
    └── renderError()
```

Le controller :

```
users.controller
    ├── init()
    ├── load()
    ├── search()
    ├── setFilter()
    ├── selectUser()
    └── destroy()
```

Le store :

```
users.store
    ├── setUsers()
    ├── getUsers()
    ├── setMeta()
    ├── getMeta()
    ├── setFilters()
    └── getFilters()
```

Le service :

```
users.service
    ├── list()
    ├── find()
    └── search()
```

---

# 14. `admin.form.js` me paraît prématuré

Pour **liste + recherche**, je ne créerais pas encore :

```
admin.form.js
```

Il n'y a pas encore de formulaire métier.

Je garderais :

```
users.controller.js
users.service.js
users.store.js
users.renderer.js
```

Puis quand on aura :

```
création
modification
groupes
permissions
profil
```

on introduira :

```
users.form.js
```

Cela évite de créer des fichiers vides "pour l'architecture".

---

# 15. HTML : oui, statique

Pour l'instant :

```
<div class="panel-card">
    <h2 class="panel-title">Utilisateurs</h2>

    <p>Recherche et consultation des utilisateurs.</p>

    <div class="section-tab">
        ...
    </div>

    <div class="tab-content">
        ...
    </div>
</div>
```

avec une structure de base déjà présente :

```
<form id="users-search-form">
    ...
</form>

<div id="users-list"></div>

<div id="users-pagination"></div>
```

Le JavaScript **anime et actualise le contenu**, mais ne crée pas le panel.

Cela respecte exactement la séparation que nous avons déjà retenue : `uiapp.js` reste responsable de la navigation globale, tandis que la feature gère sa fonctionnalité.

---

# 16. Et plus tard seulement Workbench

Si l'écran devient :

```
Users
 ├── liste
 ├── détail
 ├── profils
 ├── groupes
 ├── permissions
 ├── connexions
 └── tokens
```

là :

```
AdminUserWorkbench
```

commencera à avoir du sens.

On pourra alors transformer les composants existants :

```
features/users/
        ↓
ui/workbench/admin/
```

sans réécrire la logique API/store.

---

# 17. Une petite correction dans ton arborescence

Tu avais :

```
AdminUserListPanel.js
AdminUserDetailPanel.js
```

Je ferais plutôt :

```
assets/js/ui/workbench/admin/
    AdminUserWorkbench.js
    AdminUserListPanel.js
    AdminUserDetailPanel.js
```

**si** le Workbench devient nécessaire.

Mais aujourd'hui :

```
assets/js/features/users/
```

suffit.

---

# 18. Routes à lister maintenant

Je mettrais dans la TODO API :

```
GET /api/admin/users
GET /api/admin/users/{id}

GET /api/team/users
GET /api/team/users/{id}

# réservées pour la suite
GET /api/admin/users/like
GET /api/team/users/like

GET /api/admin/users/batch
GET /api/team/users/batch
```

Et les paramètres V1 :

```
q
group
permission
active
page
per_page
sort
order
```

mais je n'implémenterais que ceux nécessaires immédiatement :

```
q
group
permission
active
page
per_page
```

---

# 19. Ordre de travail

Je ferais effectivement les deux branches **en parallèle** :

### Backend

```
1. UserService
       ↓
2. Admin/UsersController
3. Team/UsersController
       ↓
4. routes
       ↓
5. tests curl
```

### Frontend

```
1. panel-card HTML
       ↓
2. users.service.js
       ↓
3. users.store.js
       ↓
4. users.renderer.js
       ↓
5. users.controller.js
       ↓
6. intégration Admin
       ↓
7. intégration Team
```

Mais avec un ordre précis :

```
                 UserService
                     │
          ┌──────────┴──────────┐
          ▼                     ▼
   Admin Controller       Team Controller
          │                     │
          ▼                     ▼
   /api/admin/users       /api/team/users
          │                     │
          └──────────┬──────────┘
                     ▼
              users.service.js
                     │
                users.store
                     │
              users.renderer
                     │
              panel-card HTML
```

---

## Et je garderais les tables/projets hors de ce premier lot

Tes tâches :

```
projects
project_members
user_profils
user ↔ personne
personne ↔ organisation
personne ↔ établissement
relations
```

restent essentielles pour la suite, notamment pour les permissions contextuelles. Le document `2026-09-26-006` confirme que `projects` + `project_members` sont nécessaires pour faire le lien entre permissions Shield et droits dépendant du projet.

Mais **elles ne doivent pas bloquer `GET /api/admin/users` et `GET /api/team/users`**.

### Donc, pour moi, la V1 est maintenant très nette

```
BACK
├── UserService
├── Admin/UsersController
├── Team/UsersController
├── routes
└── tests

FRONT
├── users.controller.js
├── users.service.js
├── users.store.js
├── users.renderer.js
└── panel-card statique

SHIELD
├── users
├── groups
└── permissions

PLUS TARD
├── RelationPickerDialog
├── user_profils
├── project_members
├── permissions contextuelles
└── login logs
```

**Je commencerais donc par `UserService` + les deux `UsersController`, puis seulement le JS.** C'est le point où les deux branches Admin/Team peuvent réellement avancer ensemble sans dupliquer du code.
