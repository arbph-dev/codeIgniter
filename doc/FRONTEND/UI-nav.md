# nav

Sidebar doit etre généré par script
- button.nav-toggle => button caché sur pc

## Classes css 
**RWD : Mobile first**

- nav
- closebtn
- nav-article
- nav-header-row
- nav-title
- nav-toggle
- nav-toc

### Mobile 
https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/css/ui.css#L137-L332

### PC
https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/css/ui.css#L694-L748

- .rwdnav { display: none; }


# Js

### gestion menu

2 éléments sont a gérés le header et l'element nav

2 fonctions a gérer
- openMenuPanel
- initMenu

style : 
- nav
- nav-article , nav-article open
- nav-header-row  primary-button
- 

#### openMenuPanel

```js
function openMenuPanel(index) {
  const menuPanels = qsa('.nav-article', _menu )
  menuPanels.forEach((menupanel, i) => { menupanel.classList.toggle('open', i === index) })
}
```js

_menu



```js
function initMenu(){

  _pages.forEach((  panel , index ) => { 
    const menu_panel = create('div', { class: 'nav-article' })
    const menu_panel_item = create('div', { class: 'nav-header-row  primary-button'  , text: panel.title  })
    const menu_panel_item_button = create('button', { class: 'nav-toggle', type: 'button' })
    
    menu_panel_item_button.setAttribute( 'aria-expanded', false)
    const menu_panel_item_i = create('i', { class: 'fa fa-fw fa-cogs' }) 

    const sub_menu = create( 'ul', { class: 'nav-toc'}) // ****

    
    panel.sections.forEach(( section , sindex) => {
      const subitem  = create( 'li', { text: section } )
      sub_menu.appendChild(subitem)

      subitem.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()

        // on utilse _pages donc + PANEL_OFFSET
        //console.log( "initMenu index = " + index )
        //console.log( "initMenu index + OFFSET = " + index + PANEL_OFFSET)
        switchPanel(index + PANEL_OFFSET )
        statusWrite( `Onglet actif : ${_pages[index].title}` )
        switchSection(sindex)
        
        if (window.innerWidth > 768){
          menu_panel.classList.remove('open')
        }
        else{ 
          //closeSidebar()
          closeNav() 
        }           
      })
    })

    menu_panel_item_button.appendChild(menu_panel_item_i)
    menu_panel_item.appendChild(menu_panel_item_button)
    menu_panel.appendChild(menu_panel_item)
    menu_panel.appendChild(sub_menu)
  
    _menu.appendChild(menu_panel)
    // on utilse _pages donc + PANEL_OFFSET
    menu_panel.addEventListener('click', () => {
      console.log(`menu clic panel : ${index}`)
      openMenuPanel(index)      
      //openMenuPanel(index + PANEL_OFFSET)
    })

    menu_panel.addEventListener('mouseleave', () => {
      console.log(`leave menu : ${index}`)
      if (window.innerWidth > 768){ menu_panel.classList.remove('open') }
    })
    /*
      if (window.innerWidth > 768){ menu_panel.classList.remove('open') }

    */

  })
}

```
