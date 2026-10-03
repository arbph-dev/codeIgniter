# Headers

## Elements

|Zone|Contenu|classe|
|---|---|---|
| header > div.title-layout | TITRE | --- |
| header > div.title-layout > h1.appTitle | --- | --- |
| header > div.title-layout > span.appSubtitle | --- | --- |
| header > div.header-actions | regroupe les boutons de la barre actions| --- |
| header > div.header-actions > button.rwdnav | bouton le menu mobile| --- |
| header > div.header-actions > button#fullscreenBtn | fullscreen | --- |
| header > div.header-actions > button#themeBtn | themes | --- |
| header > div.header-auth | anonymous : email + password + Connexion + Inscription| --- |

#header > div.header-auth > div.auth-form > p.auth-error



## Structure

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

## Code

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



## title-layout
Zone de titre
```html
    <header id="header">
        <div class="title-layout">
            <h1 id="appTitle">Automates industriels communicants</h1>
            <span id="appSubtitle">Comparaison, caractéristiques techniques et avis</span>
        </div>
```
### style
- title-layout     : non
- appTitle         : sur ID
- appSubtitle      : sur ID

```css
#appTitle { font-size: 1.1rem; font-weight: bold; }
#appSubtitle { font-size: 0.8rem; opacity: 0.8; }
```

## actions

```html
<div class="header-actions">
    <button class="rwdnav" type="button" onclick="openNav()" aria-label="Ouvrir le menu">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>
    <button id="themeBtn" type="button">Thème nature</button>
    <button id="fullscreenBtn" type="button">Plein écran</button>
</div>
```
### style
```css
.header-actions button {
  padding: 6px 12px;
  margin-left: 8px;
  cursor: pointer;
  border-radius: 4px;
  border: none;
  background: rgba(255, 255, 255, 0.15);
  color: white;
}
```

### JS
L'evenement onclick de rwdnav est associé a openNav() directement dans html

themeBtn et fullscreenBtn ont des évènements affectés dans la fonction setPageRef()
- fullscreenBtn listener sur click ->fullscreenSwitch
- themeBtn listener sur click -> themeSwitch

---

## auth
Cette partie est construite dynamiquement selon l'état de l'autehntification


```html
<div class="header-auth">
    <div class="auth-form">
        <p class="auth-error">Compte non activé. Vérifiez votre email.</p>
        <label class="sr-only" for="auth-email">Email</label>
        <input id="auth-email" type="email" name="email" placeholder="Email" autocomplete="username" required="">
        
        <label class="sr-only" for="auth-password">Mot de passe</label>
        <input id="auth-password" type="password" name="password" placeholder="Mot de passe" autocomplete="current-password" required="">
        
        <button type="button" class="auth-submit">
            <i class="fa fa-fw fa-sign-in" aria-hidden="true"></i>
            <span>Connexion</span></button><button type="button" class="auth-link auth-register-btn">
            <i class="fa fa-fw fa-user-plus" aria-hidden="true"></i>
            <span>Inscription</span>
        </button>
    </div>
</div>
```

### Affichage des erreurs
element : `#header > div.header-auth > div.auth-form > p.auth-error`
```
<p class="auth-error">Compte non activé. Vérifiez votre email.</p>
```html


### JS
- /assets/js/ui/workbench/auth/ToolbarAuthPanel.js
