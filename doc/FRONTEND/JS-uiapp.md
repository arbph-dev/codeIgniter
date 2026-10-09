# Point d'entrée de l'application

## Initialisation
les fonctions essentielles
- `boot()`
- `document.addEventListener("DOMContentLoaded")`
- `window.onload`

### boot

  initAuthController() // /public/assets/js/features/auth/auth.controller.js
  new ToolbarAuthPanel().init() 

  
### DOMContentLoaded

### window.onload

## Import
les imports communs au scripts applications

### features/auth
Permet de gérer les échanges avec l'API et de stocker les données  
- ['/assets/js/features/auth/auth.controller.js'](/public/assets/js/features/auth/auth.controller.js)
- ['/assets/js/features/auth/auth.store.js'](/public/assets/js/features/auth/auth.store.js)

```js
import { initAuthController }  from   '/public/assets/js/features/auth/auth.controller.js'
import { authStore }           from   '/assets/js/features/auth/auth.store.js'                      
```


### Helpers
```js
import { bus } from '/assets/js/core/eventBus.js'
import { byId, byName , qs , qsa , create } from '/assets/js/core/domhelper.js'
```

#### domhelper
- [ ] retrouver la documentation
- [ ] créer /doc/FRONTEND/JS/CORE/DOMHELPER.md

#### eventBus
- [ ] retrouver la documentation
- [ ] créer /doc/FRONTEND/JS/CORE/EVENTBUS.md


### ui/workbench/auth/

Construit la toolbar

dépend de ui/workbench/core/AuthPanelbase.js qui dépend de .... à revoir trop complexe
- [`/assets/js/ui/workbench/auth/ToolbarAuthPanel.js`](/public/assets/js/ui/workbench/auth/ToolbarAuthPanel.js)
- [`/assets/js/ui/workbench/core/AuthPanelBase.js`](/public/assets/js/ui/workbench/core/AuthPanelBase.js)

```js
import ToolbarAuthPanel        from   '/assets/js/ui/workbench/auth/ToolbarAuthPanel.js'
```




# Fonctions uiapp

- Construit le board user
- Construit le board admin
- gère les panels, leur affichage
- gère les menus, leur affichage

### [`statusWrite( textContent )`](/public/assets/js/uiapp.js#L326)
affiche un message dans le footer du document

Permet de suivre les évolutions en affichant des informations dans un élément html `footer/div#statusBar` 

- définition  [`statusWrite( textContent )`](/public/assets/js/uiapp.js#L326)


on profite de la migration de [`/assets/js/uiapp2.js`](/public/assets/js/uiapp2.js) pour les documenter

Reprendre les notes de [`Frontend.md`](/Frontend.md)

<!--

# librairies  
- [ ] Crééer  fichier et dossiers si besoin (exemple : JS-component)  : JS-libext , JS-lib , JS-component ,JS-core

```js
import { bus } from '/assets/js/core/eventBus.js'
import { byId, byName , qs , qsa , create } from '/assets/js/core/domhelper.js'
import { initMermaid } from '/assets/js/components/mermaid.js'
import { initApex } from '/assets/js/components/apex.js'
import { initCodeVal } from '/assets/js/components/codeval.js'
import { initCallout} from '/assets/js/components/callout.js'
import { initLeaflet }  from '/assets/js/components/leaflet.js'

//2026-09-22-000 ajout de auth
import { initAuthController } from '/assets/js/features/auth/auth.controller.js'
//2026-09-28-001
import { authStore } from '/assets/js/features/auth/auth.store.js'                      
import ToolbarAuthPanel       from '/assets/js/ui/workbench/auth/ToolbarAuthPanel.js'

import AdresseWorkbench from '/assets/js/ui/workbench/adresse/AdresseWorkbench.js'

import { initVoxBus } from '/assets/js/core/vox.js'
import { initVoxRenderer } from '/assets/js/core/vox.renderer.js'
import { initSceneBg }     from '/assets/js/ihm/cp_scene_bg.js'
```

### Dépendances
- ToolbarAuthPanel.js / AuthPanelBase.js


## Travaux
- [doc/notes/2026-10-03.md](/doc/notes/2026-10-03.md)

### authentification
ressources
- [`/assets/js/ui/workbench/auth/ToolbarAuthPanel.js`](/public/assets/js/ui/workbench/auth/ToolbarAuthPanel.js)
- [`/assets/js/ui/workbench/core/AuthPanelBase.js`](/public/assets/js/ui/workbench/core/AuthPanelBase.js)
- ['/assets/js/features/auth/auth.store.js'](/public/assets/js/features/auth/auth.store.js)
- ['/assets/js/features/auth/auth.controller.js'](/public/assets/js/features/auth/auth.controller.js)
```
[]()
function boot()
{
  initAuthController() // /public/assets/js/features/auth/auth.controller.js
  new ToolbarAuthPanel().init() 
  bus.subscribe('auth:loading', () => statusWrite('auth:loading') )
  
  bus.subscribe('auth:success', () => {
    mountApplication()
    mountUserBoard(authStore.user)      // contenu seulement, sans changer de panel
  })

  bus.subscribe('auth:guest',   () => noAuth())
  // ── Affichage des boards ──
  bus.subscribe('board:user',     () => switchPanel(PANEL_USER))
  bus.subscribe('board:register', () => switchPanel(PANEL_USER))
  bus.subscribe('board:admin',    () => { mountAdminBoard(authStore.user); switchPanel(PANEL_ADMIN) })
  bus.subscribe('board:hide',     () => switchPanel(_lastContentPanel))

  // login / register avec connexion immédiate / logout
  bus.subscribe('auth:changed', () => {
    if (authStore.loggedIn) switchPanel(PANEL_USER)
  })
  bus.publish('auth:check')

  window.openNav = () => { _menu.classList.add("open") } 
  window.closeNav = () => { _menu.classList.remove("open") }
```

# Gestion de l'affichage
reprendre les notes de https://github.com/arbph-dev/codeIgniter/blob/master/Frontend.md

[/assets/js/uiapp.js](/public/assets/js/uiapp.js) dispose de plusieurs fonctions 
- switchPanel : afficher masquer
- mountUserBoard(user)  :
- 

## gestion des panels
les panels sont détaillés dans la partie ui 
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/FRONTEND/UI.md
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/FRONTEND/UI-panels.md
- affichage des boards user et admin : https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-03-001-05.md


### Affichage des boards
```js
    bus.subscribe('board:user',     () => switchPanel(PANEL_USER))
  bus.subscribe('board:register', () => switchPanel(PANEL_USER))
  bus.subscribe('board:admin',    () => { mountAdminBoard(authStore.user); switchPanel(PANEL_ADMIN) })
  bus.subscribe('board:hide',     () => switchPanel(_lastContentPanel))
```

-->
