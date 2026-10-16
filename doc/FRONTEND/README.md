l'arborescence evolue, on sépare le code des composants pour les évoloutions
- on modifie les documents html
- on scinde les scripts applications


|Fichier|Responsabilité|
|---|---|
|[`index.html`](https://github.com/arbph-dev/codeIgniter/blob/master/public/index.html)|Structure de l'application|
|[`/assets/css/ui.css`](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/css/ui.css)|Styles|
|`domhelper.js`|Fonctions DOM élémentaires, thèmes et plein écran si elles y sont déjà bien intégrées|
|`domfactory.js`|Construction et montage des composants DOM|
|`domelements.js`|Navigation, pagination, menus et lecture de la structure des pages|
|`domutils.js`|Utilitaires génériques, notamment `typeofObj`|
|`shared/templates.js`|`TemplateFactory` et gestion centralisée des templates|
|`shared/templates/tpl-user.js`|Rendu des cartes utilisateur, badges de groupes et permissions|
|[`/assets/js/uibase.js`](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/uibase.js)|Contrat commun, état de navigation, cycle de vie et points d'extension|
|[`/assets/js/app-index.js`](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/app-index.js)|Initialisation et comportements spécifiques de `/index.html`|




[`index.html`](https://github.com/arbph-dev/codeIgniter/blob/master/public/index.html)
- Structure de l'application révisée

[`/assets/css/ui.css`](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/css/ui.css)
- Styles

[`/assets/js/app-index.js`](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/app-index.js)
- Initialisation et comportements spécifiques de `/index.html`

[`/assets/js/uibase.js`](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/uibase.js)
- Contrat commun, état de navigation, cycle de vie et points d'extension
- gestion menu

```js
/*  ======================================================================================================================  */
function openMenuPanel(index) {

  const menuPanels = qsa('.nav-article', _menu )

  menuPanels.forEach((menupanel, i) => {
    menupanel.classList.toggle('open', i === index)
  })

  //switchPanel(index)
}

/*  ======================================================================================================================  */
function initMenu(){

  _pages.forEach((  panel , index ) => { 
    const menu_panel = create('div', { class: 'nav-article' })
    const menu_panel_item = create('div', { class: 'nav-header-row  primary-button'  , text: panel.title  })
    const menu_panel_item_button = create('button', { class: 'nav-toggle', type: 'button' })
    
    menu_panel_item_button.setAttribute( 'aria-expanded', false)
    const menu_panel_item_i = create('i', { class: 'fa fa-fw fa-cogs' }) 

    const sub_menu = create( 'ul', { class: 'nav-toc'}) // ****

    
    panel.sections.forEach(( section , sindex) => {
      const subitem  = create( 'li', { text: section } )
      sub_menu.appendChild(subitem)

      subitem.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()

        // on utilse _pages donc + PANEL_OFFSET
        //console.log( "initMenu index = " + index )
        //console.log( "initMenu index + OFFSET = " + index + PANEL_OFFSET)
        switchPanel(index + PANEL_OFFSET )
        statusWrite( `Onglet actif : ${_pages[index].title}` )
        switchSection(sindex)
        
        if (window.innerWidth > 768){
          menu_panel.classList.remove('open')
        }
        else{ 
          //closeSidebar()
          closeNav() 
        }           
      })
    })

    menu_panel_item_button.appendChild(menu_panel_item_i)
    menu_panel_item.appendChild(menu_panel_item_button)
    menu_panel.appendChild(menu_panel_item)
    menu_panel.appendChild(sub_menu)
  
    _menu.appendChild(menu_panel)
    // on utilse _pages donc + PANEL_OFFSET
    menu_panel.addEventListener('click', () => {
      console.log(`menu clic panel : ${index}`)
      openMenuPanel(index)      
      //openMenuPanel(index + PANEL_OFFSET)
    })

    menu_panel.addEventListener('mouseleave', () => {
      console.log(`leave menu : ${index}`)
      if (window.innerWidth > 768){ menu_panel.classList.remove('open') }
    })
    /*
      if (window.innerWidth > 768){ menu_panel.classList.remove('open') }

    */

  })
}

```







`domhelper.js`
- Fonctions DOM élémentaires, thèmes et plein écran si elles y sont déjà bien intégrées
 
`domfactory.js`
- Construction et montage des composants DOM

`domelements.js`
- Navigation, pagination, menus et lecture de la structure des pages

`domutils.js`
- Utilitaires génériques, notamment `typeofObj`

`shared/templates.js`
- `TemplateFactory` et gestion centralisée des templates

`shared/templates/tpl-user.js`
- Rendu des cartes utilisateur, badges de groupes et permissions|




<!--



# application
- public/ui.html
    - [header - auth](/doc/FRONTEND/UI-header.md#auth)
- [/assets/js/uiapp.js](/public/assets/js/uiapp.js) : application
    - gestion authentification et admin
    - [Evénements](/doc/FRONTEND/JS/Events.md)


## intégration admin / ui [en cours]
- backup a détruire après évolution REFACTOR\OVH\public\ui copy.html
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/FRONTEND/UI-panels.md#panel-admin

1- le panel admin
actuellement 
- il est branché dans `/assets/js/uiapp.js` - fonction `boot` - ligne 524
- il emploie `/assets/js/uiapp.js` - fonction `mountAdminBoard` - ligne 464
- element dom associé `main#stack > div.panel-card > div.section-tab > div.tab-content > div#admin-board-body`

```js
bus.subscribe('board:admin',    () => { 
    mountAdminBoard(authStore.user); switchPanel(PANEL_ADMIN) 
})
```

évolution requise

- emploie du `<div class="tab-headers"></div>`
    - on construit manuellement le code html , on créera dynamiquement les élements par la suite quand le code sera mature

    - on utilise la logique déja en place dans `/assets/js/uiapp.js` - fonction `initNavigation` - ligne 198 
        - [] modifier mountAdminBoard ou le code html (affecter les events) et gérér le panel_header
        - let panel_header = qs( "div.section-tab > div.tab-headers" , _main_panels[PANEL_ADMIN] )   // reference sur element de PANEL_ADMIN
    

gérér le panel_header on peut reprendre la logique de  `/assets/js/uiapp.js` - fonction `initNavigation` - ligne 198

on a panel_header = qs( "div.section-tab > div.tab-headers" , _main_panels[PANEL_ADMIN] ) 
il faut panel_tabs = qsa ( "div.section-tab" , _main_panels[PANEL_ADMIN] ) 
et panel_tab_titles = qsa( "div.section-tab  > div.tab-content > h3" , main_panels[PANEL_ADMIN] )

```js
function mountAdminBoard(user) {
    //const body = document.querySelector('#admin-board-body')
    const body = byId('#admin-board-body')
    if (!body) return

    // Minimal admin : même carte + mention rôle
    const isAdmin = (user?.groups ?? []).some(g =>
        ['admin', 'superadmin'].includes(g)
    )

    body.innerHTML = `
        ${renderUserCard(user, { title: 'Administration' })}
        <hr>
        <p>${isAdmin
            ? 'Accès admin OK. Gestion utilisateurs / seeder — prochaine étape.'
            : 'Accès refusé — groupe admin requis.'}</p>
    `

    const panel_header = qs( "div.section-tab > div.tab-headers" , _main_panels[PANEL_ADMIN] ) 
    //panel_tabs = qsa ( "div.section-tab" , _main_panels[PANEL_ADMIN] ) 
    const panel_tab_titles = qsa( "div.section-tab  > div.tab-content > h3" , main_panels[PANEL_ADMIN] )

    panel_tab_titles.forEach(( section , sindex) => {
     
      if (sindex === 0 ){ //par defaut le bouton 0 est actif 
        buttonTemp = create( 'button', { type: 'button', class: 'tab-btn active', text: section } )
      }
      else{
        buttonTemp = create( 'button', { type: 'button', class: 'tab-btn', text: section } )
      }
      
      panel_header.appendChild( buttonTemp )
      buttonTemp.addEventListener('click', () => { switchSection(sindex) })

    })


}
```

### TODO 
#### Separer la creation de l'affichage

##### creation de la board
  mountAdminBoard(authStore.user) a ajouter dans `/assets/js/uiapp.js` - fonction boot - bus.subscribe('auth:success') - ligne 515
    
    ```js
    bus.subscribe('auth:success',    () => {
    ....
    mountAdminBoard(authStore.user);
    ```    

##### affichage  de la board
    mountAdminBoard(authStore.user) a retirer dans `/assets/js/uiapp.js` - fonction boot - bus.subscribe('auth:success') - ligne 527
    ```
    bus.subscribe('board:admin',    () => {switchPanel(PANEL_ADMIN) })
    ```
##### documenter Gestion event
    
- `_bindAdmin` : lie le `button.auth-link auth-board-admin` créa par ToolbarAuthPanel.js - `_buildUserBar(user)` - ligne 145 implémentation du contrat
```js
    _bindAdmin()
    {
        this._target.querySelector('.auth-board-admin')
            ?.addEventListener('click', () => bus.publish('board:admin'))
    }
```

- ToolbarAuthPanel extends AuthPanelBase 
    /assets/js/ui/workbench/auth/ToolbarAuthPanel.js - implémentation du contrat
    /assets/js/ui/workbench/core/AuthPanelBase.js - contrat ,event binding


- `AuthPanelBase.js` emploie `_buildUserBar(user)` dans `_render(state, payload )` - ligne 222
    
```js
this._buildUserBar(this._user).forEach(el => this._target.appendChild(el))
```

- this._target est initialisé par assets/js/ui/workbench/core/AuthPanelBase.js -  init() - ligne 52
```
    init()
    {
        this._target = document.querySelector(this._selector)
```


# composants
- public/ui-components/apex.html
- [/assets/js/uiapp2.js](/public/assets/js/uiapp2.js) : 




-->





---

**a deplacer vers /doc/FRONTEND/JS-Libs**

## Librairies Javascript
### Librairies tierces

### Librairies

#### core
```
/assets/js/core/domhelper.js
/assets/js/core/eventBus.js
```
#### components
```
/assets/js/components/apex.js
/assets/js/components/callout.js
/assets/js/components/codeval.js
/assets/js/components/mermaid.js
/assets/js/components/leaflet.js
```

##### vox
```
/assets/js/core/vox.js
/assets/js/core/vox.renderer.js
```
##### scene
```
/assets/js/ihm/cp_scene_bg.js
```

#### authentification

##### [`auth.controller.js`](/public/assets/js/features/auth/auth.controller.js)
```
/assets/js/features/auth/auth.controller.js
/assets/js/features/auth/auth.store.js

auth:success

```

#### workbench
```
/assets/js/ui/workbench/auth/ToolbarAuthPanel.js
/assets/js/ui/workbench/adresse/AdresseWorkbench.js
```
