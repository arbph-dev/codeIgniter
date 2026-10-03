source : [/assets/js/uiapp.js](/public/assets/js/uiapp.js)

# Point d'entrée de l'application

## Import voir librairie 
- [ ] Crééer JS-libext , JS-lib , JS-component ,JS-core


## Travaux

Suivre les évolutions avec élément html `footer/div#statusBar`

```html
<footer>
    <div id="statusBar">
        Automate actif : Schneider Modicon M221
    </div> 
</footer>
```

```js
function statusWrite( textContent ){
  if (_footer_status){ _footer_status.textContent = textContent }
  console.log("STATUS :: " + textContent)
}
```
