# UI
```
public/ui.html
public/assets/js/uiapp.js

```
# Librairies Javascript


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
