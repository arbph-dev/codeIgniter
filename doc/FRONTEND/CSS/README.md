# CSS
- 2026-09-20 : Ajout callout 
- 2026-10-08 : Ajout root
- 2026-10-10 : separation code pour projet finale :
  - [public/assets/css/ui.css](/public/assets/css/ui.css)
  - [public/index.html](/public/index.html) 

- path serveur `/assets/css/ui.css`

**Regles**
- mobile first on gere les spécifictés pc voir tablet
- les dimensions dans root
- les couleurs dans root theme
- on colle a la structure


**Coller à la structure**
on utilise l'élément dom : `main` plutôt que l'id `#stack` 

dans html
```html
<!-- <main id="stack">-->
<main>
```
ainsi pour css
```css
/* #stack { flex: 1; padding: 28px; overflow-y: auto; } */
main  { flex: 1; padding: 28px; overflow-y: auto; }
```


**essais** 
- root avec rwd
- on change les dimensions ou flex selon pc ou mobile
- a splitter en 2 line
```css
--wb-shadow:     0 2px 12px rgba(0,0,0,.08);
```

**travaux** 
certains styles sont desktop first comme pour la partie auth du header voir [public/assets/css/ui.css](/public/assets/css/ui.css#L323-L330)
Cela fonctionne mais cela doit être revue.

- .auth-link span
- .auth-username
- .auth-link
- .auth-form input
- .auth-form button span 
- .auth-form button

les classes Workbench sont a commentés et a redefinir
https://github.com/arbph-dev/codeIgniter/blob/a16d182956b00513ec0a4a88e53dde2cd49bbf04/public/assets/css/ui.css#L587-L607
https://github.com/arbph-dev/codeIgniter/blob/a16d182956b00513ec0a4a88e53dde2cd49bbf04/public/assets/css/ui.css#L609-L620


## Classes
```css
#appSubtitle { font-size : 0.8rem; opacity : 0.8;  }
#appTitle  { font-size : 1.1rem; font-weight : bold;  }
#contentLayout { display : flex; flex : 1; overflow : hidden;  }
#header { align-items : center; background-color : var(--bg-header); color : var(--text-header); display : flex; height : 60px; justify-content : space-between; padding : 0 22px;  }
.header-actions button { background : rgba(255, 255, 255, 0.15); border : none; border-radius : 4px; color : white; cursor : pointer ;margin-left : 8px; padding : 6px 12px;  }
.header-auth { align-items : center; display : flex; flex : 0 0 auto; font-size : 0.85rem; gap : 6px;  }
.rwdnav  { text-align : right ; }
.rwdnav  { display : none;  }
body { background-color : var(--bg-main); color : var(--text-main); display : flex; flex-direction : column; height : 100vh;  }
nav { background-color : var(--bg-sidebar); color : var(--text-sidebar); display : flex; flex-direction : column; inset : 0; overflow-x : hidden; overflow-y : auto; position : fixed; transform : translateX(-100%) ; transition : transform .3s ease ; z-index : 1000;  }
nav.open { transform : translateX(0);  }
nav a.closebtn { align-self : flex-end; font-size : 2rem; padding : 8px 16px; text-decoration : none;  }
nav a.closebtn { display : none;  }
```

elements

- header

- header > div.title-layout
- header > div.title-layout > h1#appTitle
- header > div.title-layout > span#appSubtitle

- header > div.header-actions
- header > div.header-actions > button.rwdnav
    - bouton pour menu sur mobile
- header > div.header-actions > button#themeBtn
- header > div.header-actions > button#fullscreenBtn

- header > div.header-auth



### [root.md](/doc/FRONTEND/CSS/root.md)
Constantes de taille et de couleur général et par thème
- `:root`
- `:root[data-theme="marine"]`
- `:root[data-theme="nature"]`

a gérer les classe wb pour workbench à regrouper en root

```css
  /* ── Espacements ─────────────────────────────────────────────────────────── */
  --wb-gap:        1rem;
  --wb-padding:    1rem;
  --wb-radius:     8px;
  --wb-shadow:     0 2px 12px rgba(0,0,0,.08);

  /* ── Panel ───────────────────────────────────────────────────────────────── */
  --wb-panel-bg:          var(--wb-white);
  --wb-panel-header-bg:   var(--wb-navy);
  --wb-panel-header-fg:   var(--wb-yellow);
  --wb-panel-header-pad:  .875rem 1rem;
  --wb-panel-body-pad:    1rem;
  --wb-left-width:        360px;
```


### [buttons.md](/doc/FRONTEND/CSS/buttons.md)
