# Librairies

## Core
- [`/assets/js/core/clientinfo.js`](/public/assets/js/core/clientinfo.js)
- [`/assets/js/core/domhelper.js`](/public/assets/js/core/domhelper.js)
- [`/assets/js/core/eventBus.js`](/public/assets/js/core/eventBus.js)


# clientinfo
path js     : [`/assets/js/core/clientinfo.js`](/public/assets/js/core/clientinfo.js)
dépendnace  : [`/assets/js/core/eventBus.js`](/public/assets/js/core/eventBus.js)   

Sonde les capacités du navigateur et les publie sur le bus ('client:info').
Utile pour les décisions d'interface que le CSS ne peut pas prendre (présence du tactile, permissions, speech synthesis, connexion réseau, etc.)

Usage :
```js
import { probeClientCapabilities } from '/assets/js/core/clientinfo.js'
const info = await probeClientCapabilities()
```
