
# Balise et attribut ARIA

```html
 <div class="panel-card hidden" data-role="admin" data-index="-2">
```

```js
const admin = document.querySelector('div.panel-card[data-role="admin"]')
const user =  document.querySelector('div.panel-card[data-role="user"]')
const boards = qsa('div.panel-card:not([data-role])', _main)
const board = document.querySelector(`div.panel-card[data-role="${role}"]`)

```
