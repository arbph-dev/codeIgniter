# Librairies

## 7. Noyau & Utilitaires


### 7.1 EventBus (Bus d'événements)
- path-js     : [`/assets/js/core/eventBus.js`](/public/assets/js/core/eventBus.js)

| Aspect | Fichier | Statut | Notes |
|--------|---------|--------|-------|
| **JS** | `core/eventBus.js` | ✅ Critique | Colonne vertébrale |
| **Pattern** | Pub/Sub | ✅ Oui | `bus.publish()`, `bus.subscribe()` |
| **Import** | Partout | ✅ Oui | Utilisé par tous les composants |

```javascript
import { bus } from '/assets/js/core/eventBus.js'

bus.publish('mon:event', { data: 'valeur' })
bus.subscribe('mon:event', (payload) => { ... })
```

---

### 7.2 DomHelper (Utilitaires DOM)
- path-js     : [`/assets/js/core/domhelper.js`](/public/assets/js/core/domhelper.js)

| Aspect | Fichier | Statut | Notes |
|--------|---------|--------|-------|
| **JS** | `core/domhelper.js` | ✅ Utilitaire | `byId()`, `qs()`, `qsa()`, `autocomplete()` |
| **Import** | `index.php:46` | ✅ Oui | Utilisé pour manipulation DOM |
| **Init** | `index.php:157` | ✅ Oui | `domhelper.init()` |

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

Sonde les capacités du navigateur et les publie sur le bus ('client:info').

Utile pour les décisions d'interface que le CSS ne peut pas prendre (présence du tactile, permissions, speech synthesis, connexion réseau, etc.)

| Aspect | Fichier | Statut | Notes |
|--------|---------|--------|-------|
| **JS** | `core/clientinfo.js` | ✅ Actif | Détecte: tactile, Web Speech, géoloc, etc. |
| **Fonction** | `probeClientCapabilities()` | ✅ Async | Publie `client:info` |
| **Init** | `index.php:169` | ✅ À window.load | Appelée après DOMContentLoaded |

```javascript
// Import
import { probeClientCapabilities } from '/assets/js/core/clientinfo.js'
// Initialisation 
const info = await probeClientCapabilities()  // Publie 'client:info'
```
