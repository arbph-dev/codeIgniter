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

- title-layout
- appTitle
- appSubtitle
- header-actions

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
```


|Zone|Contenu|
|---|---|
|.header-auth (guest)|email + password + Connexion + **Inscription**|
|.header-auth (register en cours)|bouton « Retour » minimal (optionnel)|

# Librairies Javascript

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

## Panels / Onglets
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

## Librairies tierces

```
import { bus } from '/assets/js/core/eventBus.js'
import { byId, byName , qs , qsa , create } from '/assets/js/core/domhelper.js'

import { initMermaid } from '/assets/js/components/mermaid.js'
import { initApex } from '/assets/js/components/apex.js'
import { initCodeVal } from '/assets/js/components/codeval.js'
import { initCallout} from '/assets/js/components/callout.js'
import { initLeaflet }  from '/assets/js/components/leaflet.js'

//2026-09-22-000 ajout de auth
import { initAuthController } from '/assets/js/features/auth/auth.controller.js'
import { authStore } from '/assets/js/features/auth/auth.store.js'                      //2026-09-28-001

import ToolbarAuthPanel       from '/assets/js/ui/workbench/auth/ToolbarAuthPanel.js'
import AdresseWorkbench from '/assets/js/ui/workbench/adresse/AdresseWorkbench.js'

//2026-09-23-001 ajout de vox
import { initVoxBus } from '/assets/js/core/vox.js'
import { initVoxRenderer } from '/assets/js/core/vox.renderer.js'

//2026-09-23-002 ajout de vox
import { initSceneBg }     from '/assets/js/ihm/cp_scene_bg.js'
```
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
```
### workbench
```
/assets/js/ui/workbench/auth/ToolbarAuthPanel.js
/assets/js/ui/workbench/adresse/AdresseWorkbench.js
```
