# Ressources
[UI.md](/doc/FRONTEND/UI.md)
- [UI-panels.md](/doc/FRONTEND/UI-panels.md)
- [UI-nav.md](/doc/FRONTEND/UI-nav.md)
- [JS-uiapp.md](/doc/FRONTEND/JS-uiapp.md)
- [CSS-root.md](/doc/FRONTEND/CSS-root.md)
- [CSS-buttons.md](/doc/FRONTEND/CSS-buttons.md)


## CSS

### [CSS-root.md](/doc/FRONTEND/CSS-root.md)
Constantes de taille et de couleur général et par thème
- `:root`
- `:root[data-theme="marine"]`
- `:root[data-theme="nature"]`

a gérer les classe wb pour workbench à regrouper en root

```css
  /* ── Espacements ─────────────────────────────────────────────────────────── */
  --wb-gap:        1rem;
  --wb-padding:    1rem;
  --wb-radius:     8px;
  --wb-shadow:     0 2px 12px rgba(0,0,0,.08);

  /* ── Panel ───────────────────────────────────────────────────────────────── */
  --wb-panel-bg:          var(--wb-white);
  --wb-panel-header-bg:   var(--wb-navy);
  --wb-panel-header-fg:   var(--wb-yellow);
  --wb-panel-header-pad:  .875rem 1rem;
  --wb-panel-body-pad:    1rem;
  --wb-left-width:        360px;
```



# en cours
- gestion authentification et admin

---
## intégration admin / ui
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
