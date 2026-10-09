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
- [boot](#boot) : démarre le script
- Construit le board user
- Construit le board admin
- gère les panels, leur affichage
- gère les menus, leur affichage


### [`boot()`](/public/assets/js/uiapp2.js#L460)

### [`statusWrite( textContent )`](/public/assets/js/uiapp.js#L326)
affiche un message dans le footer du document

Permet de suivre les évolutions en affichant des informations dans un élément html `footer/div#statusBar` 

- définition  [`statusWrite( textContent )`](/public/assets/js/uiapp.js#L326)


on profite de la migration de [`/assets/js/uiapp2.js`](/public/assets/js/uiapp2.js) pour les documenter

Reprendre les notes de [`Frontend.md`](/Frontend.md)


<!-- 
[](#)
### [``](/)


### [``](/)

- boot : public/assets/js/uiapp2.js#L460

fullscreenSwitch
#fullscreenSwitch


- onload
- [readPage](#readPage)
- [setPageRef](#setPageRef)
- [statusWrite](#statusWrite)
	- Ecrit dans le footer en utilisant **_footer_status**, si **_footer_status** est null  fallback vers console
- [themeSwitch](#themeSwitch)
- typeofObj
	- a sortir vers domHelper
 	- `console.log( typeofObj( _menu ) )`

Navigation

- [initMenu](#initMenu)
- [initNavigation](#initNavigation)
- [initPagination](#initPagination)

- [openMenuPanel](#openMenuPanel)


- Sidebar
	- [closeSidebar](#closeSidebar)
	- [initSidebar](#initSidebar)
	- [openSidebar](#openSidebar)

- openMenuPanel
- openNav
- closeNav


show/hide boards
- switchPanel
- switchSection

```



- getAuthBoards
- hideAuthBoards

- initAuthBoards


- mountAdminBoard
- mountApplication
- mountUserBoard

- noAuth



- show
- showAuthBoard
```


## metadata

### readPage
```js
  _main_panels = qsa('div.panel-card:not([data-role])', _main) // ignorer les panels auth
  //_main_panels = qsa("div.panel-card" , _main )  // on extrait les informations de la page
```

```js

```
**2026-09-27-002**
- Modifier readPage() pour ignorer les panels auth :
**2026-09-30-001**
- offset readpage à formaliser

### setPageRef
Initialise des références aux principaux elements
- certains ne servent qu'une fois donc les déplacer en init pour minimiser la mémoire

Appelle [readPage](#readPage) pour initialiser le tableau 

Appele les différentes initialisation des éléments de la navigation
- [initMenu](#initMenu)
- [initNavigation](#initNavigation)
- [initPagination](#initPagination)

#### Structure du contenu
```
main#stack
	div.panel-card  = article
		h2.panel-title
		p.panel-description 
		div.section-tab
			div.tab-headers
			div.tab-content = section
				h3
				p
```



| variable | référence | Note |
| --- | --- | --- |
| _footer | footer | reference sur name , footer du body |
| _footer_status | footer / div#statusBar | reference sur selecteur css depuis _footer |
| _header_actions_btn_fullscreen | header#header > div.header-actions > button#fullscreenBtn | reference sur selecteur css |
| _header_actions_btn_theme | header#header > div.header-actions > button#themeBtn | reference sur selecteur css |
| _main | main | reference sur name, norme dit main est unique , id stack ne sert a rien |
| _menu | nav | reference sur name , nav du body |

**header#header** n'est pas référencé

**byName** retourne une collection

```js
  _main = byName("main")[0]
  _menu = byName( "nav", document )[0]

  _footer = byName("footer" , document )[0]
  _footer_status = qs( "div#statusBar" , _footer )
  
  _header_actions_btn_fullscreen = qs( "header#header > div.header-actions > button#fullscreenBtn")
  _header_actions_btn_fullscreen.addEventListener("click", fullscreenSwitch );// Gestion du plein écran

  _header_actions_btn_theme = qs( "header#header > div.header-actions > button#themeBtn")
  _header_actions_btn_theme.addEventListener("click", themeSwitch );// Gestion du thème - click header

  if ( !readPage() ) { return }
  
  initPagination()    
  initNavigation()
  initMenu()
```

## show/hide boards
- Modifier readPage() pour ignorer les panels auth :
- ajout function getAuthBoards()
- ajout function hideAuthBoards()
- ajout function showAuthBoard(role)
- ajout function initAuthBoards() 


## Helper de rendu
Rendu minimal board user / admin
- function badgeGroups(groups)
- function badgePerms(permissions)
- function renderUserCard(user, { title = 'Mon profil' } = {})
	- Carte profil minimale des données endpoint /me { id, username, email, groups, permissions }

#### Montage dans les boards 
fait sentir la nécessité d'un panel user ou workbench
- function mountUserBoard(user) {
- function mountAdminBoard(user) {

####  Branchement bus
- import '/assets/js/features/auth/auth.store.js'
- remplacer initAuthBoards
- Affiche panel user , le formulaire est déjà monté par AuthPanelBase._mountRegisterForm.
```js
  bus.subscribe('board:register', () => showAuthBoard('user'))
```
## Génération de la structure HTML

Pour chaque page contenue dans _pages
 - le code crée un panneau de navigation (menu_panel) comprenant
  - un titre (panel.title)
  - un bouton avec une icône
  - et un sous-menu (sub_menu).

## Création des sous-sections

Pour chaque section d'une page
 Le code génère un élément de liste (<li>) et l'ajoute au sous-menu.

# Gestion de la navigation 

## sidebar

#### closeSidebar
exploite la référence **_menu**
```js
function closeSidebar() { _menu.classList.remove("open") }
```
#### initSidebar
```js
function initSidebar() {
    bus.subscribe('sidebar:open', openSidebar)
    bus.subscribe('sidebar:close', closeSidebar)    
    window.openNav = () => { bus.publish('sidebar:open') }
    window.closeNav = () => { bus.publish('sidebar:close') }
}
```
#### openSidebar
exploite la référence **_menu**
```js
function openSidebar() { _menu.classList.add("open") }
```

(clic sur une section)
Lorsqu'un utilisateur clique sur un élément du sous-menu :
L'action par défaut et la propagation de l'événement sont stoppées (preventDefault, stopPropagation).

Les fonctions **switchPanel(index)** et **switchSection(sindex)** sont exécutées pour afficher le contenu correspondant.

Le menu se ferme automatiquement (en retirant la classe 'open' sur grand écran ou en appelant closeSidebar() sur mobile).


### initMenu
Ce code JavaScript initialise dynamiquement un menu de navigation déroulant ou accordéon à partir d'un tableau de données nommé **_pages** initialisé par [readPage](#readPage)

- [/assets/js/uiapp.js - initMenu - #L193](public/assets/js/uiapp.js#L193)

### initNavigation
on ajoute dans chaque panel contenues dans **_pages** la barrre de navigations panel
un panel 
"div.section-tab > div.tab-headers"

### initPagination‎
utilise **_pages** initialisé par [readPage](#readPage)
- doit disparaitre a terme

## Interactions avec le panneau principal :

### openMenuPanel
Au clic : Ouvre ou bascule le panneau via openMenuPanel(index).
- [/assets/js/uiapp.js - openMenuPanel - #L181](public/assets/js/uiapp.js#L181)



-->














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
