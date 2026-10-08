# Points importants

comment devenir superadmin car distinction existante : admin, superadmin


donner des ressources contexte
	liste des champs des models cast / validate

différence `_pages` et `_main_panels` 
    `_pages` stocke les panels générique pour le contenu
    `_main_panels` stocke tous les panels, dont board admin et user

**a retenir** : _main_panels[index+PANEL_OFFSET] equivaut à _pages[index]

ce que cela induit
- switchPanel(PANEL_ADMIN) fonctionne avec `_main_panels` , PANEL_ADMIN = 0
- pour les _pages switchPanel(index+PANEL_OFFSET)  => 2 premier panel générique de contenu


- ToolbarAuthPanel extends AuthPanelBase 
    /assets/js/ui/workbench/auth/ToolbarAuthPanel.js - implémentation du contrat
    /assets/js/ui/workbench/core/AuthPanelBase.js - contrat ,event binding
https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-03-001.md#-qui-fait-quoi--toolbarauthpanel-vs-authpanelbase


**autorisation**
a mettre par la suite

```
if (! auth()->user()?->can('admin.access'))
```

dans chaque méthode.

L'accès `/api/admin/*` doit idéalement être protégé par le **filtre Shield / groupe / permission**, comme le reste de l' API.

 Le contrôleur ne doit pas dupliquer cette politique à chaque endpoint.

```
$routes->group('api/admin', [
    'filter' => 'permission:admin.access',
], static function ($routes) {
    ...
});
```


Point à vérifier avant test : 

config('Auth')->tables doit bien être accessible dans ton App\Config\Auth. 

Shield utilise justement une configuration de noms de tables pour identities, groups_users et permissions_user




https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/src/Models/UserModel.php
https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/src/Entities/User.php
https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/src/Entities/UserIdentity.php


**throttle** - https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-04-001.md#%C3%A0-ne-pas-oublier--le-throttle





ui panels / gestion affichage / active


      if (sindex === 0 ){ //par defaut le bouton 0 est actif 
        buttonTemp = create( 'button', { type: 'button', class: 'tab-btn active', text: section } )
      }
      else{
        buttonTemp = create( 'button', { type: 'button', class: 'tab-btn', text: section } )
      }
