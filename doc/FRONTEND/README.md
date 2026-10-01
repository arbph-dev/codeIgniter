- [ ] Headers / js : Faire un choix affectation event ui dans html ou dans le code js
- [ ] Revoir nécessité des id sur les éléments de structure main, header, nav ; but simplifier les selectors et le code css
- [ ] Panels - Onglets / Structure : panel-card a faire évoluer en article et div.section-tab en sections

# UI
```
public/ui.html
public/assets/js/uiapp.js
public/assets/css/style.css
```


# Structure du document

```
header id="header"
nav id="sidebar"

```
## Headers

### Elements

|Zone|Contenu|
|---|---|
|header > div.header-actions | regroupe les boutons |
|header > div.header-auth | anonymous : email + password + Connexion + Inscription|
|header > div.header-auth | anonymous : email + password + Connexion + Inscription|
|header > div.header-auth |register en cours|

```
header#header
    div.title-layout
        h1.appTitle
        span.appSubtitle
    div.header-actions
        button.rwdnav
            i.fa fa-bars
        button#themeBtn
        button#fullscreenBtn
    div.header-auth
```


```html
    <header id="header">
        <div class="title-layout">
            <h1 id="appTitle">Automates industriels communicants</h1>
            <span id="appSubtitle">Comparaison, caractéristiques techniques et avis</span>
        </div>

        <div class="header-actions">
            <button class="rwdnav" type="button" onclick="openNav()" aria-label="Ouvrir le menu">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
            <button id="themeBtn" type="button">Thème nature</button>
            <button id="fullscreenBtn" type="button">Plein écran</button>
        </div>

        <div class="header-auth">
            
        </div>
    </header>
```

### CSS
- title-layout
- appTitle
- appSubtitle
- header-actions

### JS
`header > div.header-actions` comporte des boutons
- `header > div.header-actions > button.rwdnav` pour le menu appelle public/assets/js/uiapp.js - openNav()
- `header > div.header-actions > button#fullscreenBtn`
- `header > div.header-actions > button#themeBtn`

```

```

themeBtn et fullscreenBtn ont des évènements affectés dans la fonction setPageRef()
- fullscreenBtn listener sur click ->fullscreenSwitch
- themeBtn listener sur click -> themeSwitch







## Sidebar

Sidebar doit etre généré par script
- button.nav-toggle => button caché sur pc

### Classes css 
- closebtn
- nav-article
- nav-header-row
- nav-title
- nav-toggle
- nav-toc

## Panels - Onglets
### structure
```html
<div class="panel-card">
    <h2 class="panel-title">...</h2>

    <p class="panel-description">.....</p>

    <div class="section-tab">
        <div class="tab-headers">
          <button class="tab-btn active">...</button>
          <button class="tab-btn" data-tab="info-0">Informations</button>
        </div>


        <div id="..." class="tab-content active">
        </div>

        <div id="..." class="tab-content">
        </div>
    </div>

</div>   
```

### css associe
- panel-card
- panel-title
- panel-description
- section-tab
- tab-headers
- tab-btn et tab-btn active
- tab-content et tab-content active

# Librairies Javascript
## Librairies tierces

## Librairies

### core
```
/assets/js/core/domhelper.js
/assets/js/core/eventBus.js
```
### components
```
/assets/js/components/apex.js
/assets/js/components/callout.js
/assets/js/components/codeval.js
/assets/js/components/mermaid.js
/assets/js/components/leaflet.js
```

#### vox
```
/assets/js/core/vox.js
/assets/js/core/vox.renderer.js
```
#### scene
```
/assets/js/ihm/cp_scene_bg.js
```

### authentification
```
/assets/js/features/auth/auth.controller.js
/assets/js/features/auth/auth.store.js

auth:success

```

### workbench
```
/assets/js/ui/workbench/auth/ToolbarAuthPanel.js
/assets/js/ui/workbench/adresse/AdresseWorkbench.js
```
