# Gestion users (user et admin)

**Ressources**
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/administration.md
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/ADMIN/index.md
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/ADMIN/UserService-update.md

<!-- 🎯 ✅ ⚠️ 📊  -->



# Version 1
- 🎯 app/Services/Users/UserService.php
- 🎯 Admin/UsersController.php fichier : Admin_UsersController.php => app/Controllers/Api/Admin/UsersController.php
    - contrôleur mince : autorisation Admin → lecture des paramètres HTTP → appel UserService → ApiResponse.
- 🎯 Team/UsersController.php fichier : Team_UsersController.php : app/Controllers/Api/Team/UsersController.php

⚠️ Point important : Shield v1.3.0 enrichit les User avec groupes/permissions via withGroups() / withPermissions(), mais l’email reste une identité séparée.


**UserService.php V1.05 : fix**
- Auth::$tables est utilisé, donc aucun nom de table Shield n'est codé en dur.
- q cherche username, id et email uniquement sur l'identité email_password.
- group, permission et active sont appliqués en SQL avant LIMIT/OFFSET.
- total et pages correspondent réellement aux filtres.
- User est enrichi par withIdentities(), withGroups() et withPermissions(), mécanisme officiellement supporté dans Shield 1.3.0.
- search() reste volontairement léger pour les futurs autocomplete/RelationPickerDialog.

Aucun secret d'identité autre que l'email n'est renvoyé dans le DTO.

Une optimisation pourra venir plus tard :
-  list() fait actuellement un second chargement Shield par utilisateur, donc il y a un N+1. 

Pour une V1 fonctionnelle, je préfère le conserver :
- on laisse Shield construire proprement les User plutôt que de reproduire sa logique d'enrichissement dans notre service.

✅ les routes :
```
$routes->group('api/admin', static function ($routes) {
    $routes->get('users', 'Api\Admin\UsersController::index');
    $routes->get('users/search', 'Api\Admin\UsersController::search');
    $routes->get('users/(:num)', 'Api\Admin\UsersController::show/$1');
});

$routes->group('api/team', static function ($routes) {
    $routes->get('users', 'Team\UsersController::index');
    $routes->get('users/search', 'Team\UsersController::search');
    $routes->get('users/(:num)', 'Team\UsersController::show/$1');
});
```

---

## 🎯 Intégration Version 1

**fichiers** : G:\WWW\REFACTOR\OVH\temp\USER\

- ✅ upload : app/Services/Users/UserService.php
- ✅ upload : app/Controllers/Api/Admin/UsersController.php
- ✅ upload : app/Controllers/Api/Team/UsersController.php
- [ ] upload : features/users/renderer.js 
- [ ] upload : features/users/service.js a séparer du renderer.js
- [ ] intégration Panel HTML minimal

```
<div class="panel-card" data-index="6">
    <h2 class="panel-title">Utilisateurs</h2>
    <p>Consultation des utilisateurs de l'application.</p>

    <div class="section-tab">
        <h3>Liste des utilisateurs</h3>

        <div id="users-renderer" class="feature-renderer" data-feature="users">
            Chargement...
        </div>

    </div>
    
</div>
```


# 📊 Version 2

Endpoint : GET /api/admin/users - Version 2
```
[ ] GET /api/admin/groups
[ ] GET /api/admin/permissions
[ ] GET /api/admin/users?group=
[ ] GET /api/admin/users?permission=
[ ] GET /api/admin/security/login-logs
```



🎯 a gérer en utilitaire pour les listes de choix , radio et checkbox

```
'groups' => $u->getGroups(),
'permissions' => array_keys($u->getPermissions()),
```

Le futur écran devra être fait en dur dans la page 
- décharge la charge du script 
- considérer si la clef est le Workbench ou le panel qui a ses tab
