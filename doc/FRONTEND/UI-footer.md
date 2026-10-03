
Permet d'informer des évolutions avec élément html footer/div#statusBar

élément html : `footer/div#statusBar`

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
