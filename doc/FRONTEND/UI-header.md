# Headers

## Elements

|Zone|Contenu|
|---|---|
| header > div.header-actions | regroupe les boutons |
| header > div.header-actions | regroupe les boutons |
| header > div.header-actions > button.rwdnav | pour le menu mobile|
| header > div.header-actions > button#fullscreenBtn | fullscreen |
| header > div.header-actions > button#themeBtn | themes |
| header > div.header-auth | anonymous : email + password + Connexion + Inscription|
| header > div.header-auth | register en cours|

```
header#header
    div.title-layout
        h1.appTitle
        span.appSubtitle
    div.header-actions
        button.rwdnav
            i.fa fa-bars
        button#themeBtn
        button#fullscreenBtn
    div.header-auth
```

`header > div.header-actions` comporte des boutons
- `header > div.header-actions > button.rwdnav` pour le menu
- `header > div.header-actions > button#fullscreenBtn`
- `header > div.header-actions > button#themeBtn`


```html
    <header id="header">
        <div class="title-layout">
            <h1 id="appTitle">Automates industriels communicants</h1>
            <span id="appSubtitle">Comparaison, caractéristiques techniques et avis</span>
        </div>

        <div class="header-actions">
            <button class="rwdnav" type="button" onclick="openNav()" aria-label="Ouvrir le menu">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
            <button id="themeBtn" type="button">Thème nature</button>
            <button id="fullscreenBtn" type="button">Plein écran</button>
        </div>

        <div class="header-auth">
            
        </div>
    </header>
```

### CSS
- title-layout
- appTitle
- appSubtitle
- header-actions

### JS


themeBtn et fullscreenBtn ont des évènements affectés dans la fonction setPageRef()
- fullscreenBtn listener sur click ->fullscreenSwitch
- themeBtn listener sur click -> themeSwitch
