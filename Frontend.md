**Fichiers**
- REPO : [/ui.html](/public/ui.html)
- RESSOURCES :
 -  https://github.com/arbph-dev/codeIgniter-appCms/blob/main/assets/ui_html.md

Premiere priorité stabiliser l' ihm 

Stop à la construction dynamique à réserver au workbench ou composant 




## Gestion des panneaux 
Les panels sont décrits ici : 
- https://github.com/arbph-dev/codeIgniter-appCms/blob/main/assets/ui_html.md#structure
- https://github.com/arbph-dev/codeIgniter-appCms/blob/main/assets/ui_html.md#css-associe

On a introduit deux types de panels 
### div.panel-card + data-role 
- [admin](/public/ui.html#L69 )
- [user](/public/ui.html#L82)

```html
 <div class="panel-card hidden" data-role="admin" data-index="-2">
```

### div.panel-card
```html
 <div class="panel-card" data-index="0">
```
- [user](/public/ui.html#L82)






La liste des Panels du menu est une liste sans panneau admin,user que l' on a dissocier mais il y a un bug à l' affichage 
 
La fonction qui gère les panneaux doit cacher tout les Panels et se séparer de l affichage 
 
La liste des Panels du menu est une liste sans panneau admin,user
La liste des panels a masqué c est toute la liste 
Pour l affichage une div panel-card est dans la liste des pages, on utilise un offset 2 car deux panels admin user selon auth user



 [/assets/js/uiapp.js#L185](public/assets/js/uiapp.js#L185)
 

# fonctions principales



- [initMenu](#initMenu)
- [initNavigation](#initNavigation)
- [initPagination](#initPagination)

- [openMenuPanel](#openMenuPanel)

- [readPage](#readPage)
- [setPageRef](#setPageRef)
- typeofObj
	- a sortir vers domHelper
 	- `console.log( typeofObj( _menu ) )`


```
- themeSwitch
- fullscreenSwitch
- switchPanel
- switchSection
- statusWrite

- openMenuPanel


- openSidebar
- closeSidebar
- initSidebar

- openNav
- closeNav

- boot
- getAuthBoards
- hideAuthBoards

- initAuthBoards


- mountAdminBoard
- mountApplication
- mountUserBoard

- noAuth
- onload


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

## Gestion de la navigation (clic sur une section)
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

Survol / Sortie de la souris (mouseleave) : 
 Sur écran d'ordinateur (largeur > 768px), le panneau se ferme automatiquement dès que la souris quitte sa zone.
