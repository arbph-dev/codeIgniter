# CSS

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
