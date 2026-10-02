- [ ] Headers / js : Faire un choix affectation event ui dans html ou dans le code js
- [ ] Revoir nécessité des id sur les éléments de structure main, header, nav ; but simplifier les selectors et le code css
- [ ] Panels - Onglets / Structure : panel-card a faire évoluer en article et div.section-tab en sections















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

#### [`auth.controller.js`](/public/assets/js/features/auth/auth.controller.js)
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
