source : [/assets/js/uiapp.js](/public/assets/js/uiapp.js)

# Point d'entrée de l'application

Suivre les évolutions avec élément html `footer/div#statusBar` et employer  [`statusWrite( textContent )`](/public/assets/js/uiapp.js#L326)

## Import voir librairie 
- [ ] Crééer  fichier et dossiers si besoin (exemple : JS-component)  : JS-libext , JS-lib , JS-component ,JS-core

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
//2026-09-28-001
import { authStore } from '/assets/js/features/auth/auth.store.js'                      
import ToolbarAuthPanel       from '/assets/js/ui/workbench/auth/ToolbarAuthPanel.js'

import AdresseWorkbench from '/assets/js/ui/workbench/adresse/AdresseWorkbench.js'

import { initVoxBus } from '/assets/js/core/vox.js'
import { initVoxRenderer } from '/assets/js/core/vox.renderer.js'
import { initSceneBg }     from '/assets/js/ihm/cp_scene_bg.js'
```
## Travaux
- [doc/notes/2026-10-03.md](/doc/notes/2026-10-03.md)

### authentification
resource
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
```



