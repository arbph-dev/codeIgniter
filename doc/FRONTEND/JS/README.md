# Librairies

## 7. Noyau & Utilitaires


### 7.1 EventBus (Bus d'événements)
- path-js     : [`/assets/js/core/eventBus.js`](/public/assets/js/core/eventBus.js)

**Pattern Pub/Sub ** : publish() / subscribe()

```javascript
// =============================================================================
//   evt       — événement DOM (ignoré, présent pour compatibilité onclick)
//   eventName — nom de l'événement bus
//   payload   — données transmises aux subscribers

window.eventBusPublish = (evt, eventName, payload = null) => {
    bus.publish(eventName, payload)
}
```


Disponible dès que eventBus.js est importé par n'importe quel module

**Usage** : 
```javascript
import { bus } from '/assets/js/core/eventBus.js'

bus.publish('mon:event', { data: 'valeur' })
bus.subscribe('mon:event', (payload) => { ... })
```

```html
<button onclick="window.eventBusPublish(event,'codeval:eval',{ id : 'CV_1'})">
  Evaluate
</button>
```







---

### 7.2 DomHelper (Utilitaires DOM)
- path-js     : [`/assets/js/core/domhelper.js`](/public/assets/js/core/domhelper.js)

✅ Utilitaire 
- `byId()`
- `qs()`
- `qsa()`
- `autocomplete()`


```javascript
import * as domhelper from '/assets/js/core/domhelper.js'

const el = domhelper.byId('myId')          // getElementById
const el = domhelper.qs('selector', parent)  // querySelector
const els = domhelper.qsa('selector', parent)  // querySelectorAll
const ac = domhelper.autocomplete({...})   // Autocomplete widget
```

---

### 7.3 ClientInfo (Détection capacités)
- path-js     : [`/assets/js/core/clientinfo.js`](/public/assets/js/core/clientinfo.js)
- dépendance  : [`/assets/js/core/eventBus.js`](/public/assets/js/core/eventBus.js)   

✅ Async       💻 Publie `client:info`


Sonde les capacités du navigateur et les publie sur le bus ('client:info').

Utile pour les décisions d'interface que le CSS ne peut pas prendre (présence du tactile, permissions, speech synthesis, connexion réseau, etc.)

**Usage** : window.load après DOMContentLoaded
```javascript
// Import
import { probeClientCapabilities } from '/assets/js/core/clientinfo.js'
// Initialisation 
const info = await probeClientCapabilities()  // Publie 'client:info'
```
