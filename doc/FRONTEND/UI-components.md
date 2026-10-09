Index
- APEX
- CALLOUT
- CODEVAL
- LEAFLET
- MERMAID
- SCENE
- VOX

Pour exploiter les composants il faut
- importer le script du composant et parfois la librairie : fichier ou CDN (apex, leaflet, mermaid) 
- initiliaser le composant

# APEX

en ligne : https://zealot.fr/ui-components/apex.html

- source :  [assets/js/components/apex.js](/public/assets/js/components/apex.js)
- import et initialisation : 
```js
import { initApex } from '/assets/js/components/apex.js'

initApex()
```

# CALLOUT
- en ligne :
- ressource :
- import et initialisation : 
```js
  // initCallout()
```

# CODEVAL
- en ligne :
- ressource :
- import et initialisation : 
```js
  initCodeVal()
```

# LEAFLET


# MERMAID
- en ligne :
- ressource :
- import et initialisation : 
```js
import { initMermaid } from '/assets/js/components/mermaid.js'
initMermaid()
```



# SCENE
- en ligne :
- ressource :
- import et initialisation : 
```js
import { initSceneBg }     from '/assets/js/ihm/cp_scene_bg.js'

initSceneBg()
```


# VOX
- en ligne :
- ressource :
- import et initialisation : 
```js
import { initVoxBus } from '/assets/js/core/vox.js'
import { initVoxRenderer } from '/assets/js/core/vox.renderer.js'

initVoxRenderer()
initVoxBus()
```

<!-- 

```js
  //initLeaflet() -> ne pas utiliser
```
switchPanel n'utilise pas _pages
- switchPanel(PANEL_ADMIN)
- switchPanel(PANEL_USER)
-->


