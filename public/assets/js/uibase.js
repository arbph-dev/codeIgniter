// /assets/js/uibase.js
/**
 * Contrat commun de l'interface utilisateur.
 *
 * Responsabilités :
 * - références communes au DOM ;
 * - état de navigation ;
 * - affichage des articles et sections ;
 * - thème, plein écran et barre de statut ;
 * - points d'extension pour les applications concrètes.
 *
 * Hors périmètre :
 * - lecture détaillée de la structure HTML : domelements.js ;
 * - construction de composants DOM : domfactory.js ;
 * - authentification : contrôleur dédié ;
 * - initialisation des bibliothèques : app-index.js ou composants.
 * 
 * 2026-10-12 voir ligne 204 /assets/js/uibase.js + ligne 90 /assets/js/app-index.js.js
 * 2026-10-15 : 
 * - modifier renderNavigation pour ajout de navigation de niveau article, menu des sections en haut
 * 
 * 
 * TODO
 * - menu a ajouter avec pages pas les articles
 * 
 */

"use strict";

import { byId, byName , qs , qsa , create } from '/assets/js/core/domhelper.js'
//import { readPage } from "/assets/js/core/domelements.js";//this.main


export class UiBase {

    constructor() {
        // Références DOM communes
        this.main = null;
        this.menu = null;
        this.footer = null;
        this.footerStatus = null;
        this.themeButton = null;
        this.fullscreenButton = null;
        
        this.articles = []; // ref aux elements de structure necessaire ??
        this.currentPanel = null;
        this.currentSection = 0;
        this.lastContentPanel = null;

        this.pages = [] //  pour menu  <> this.articles 

        this.currentTheme = "marine";
        // boot guard: prevent multiple boots across subclasses
        this._booted = false;        
    }

    // ============================================================
    // Cycle de vie
    // ============================================================
    
    /* Point d'entrée obligatoire de l'application concrète. DOIT  être implémenté dans app-index.js.     */
    boot() { throw new Error("UiBase.boot() doit être implémentée.") }
    
    // ---------------------------
    // Boot guard helpers
    /** Ensure boot is executed only once for this instance. Returns true the first time it is called, false afterwards.     */
    ensureBooted() {
        if (this._booted) return false;
        this._booted = true;
        return true;
    }
    
    /** Read-only check whether instance has been booted. */
    isBooted() { return !!this._booted; }
    
    /**
     * Test helper to reset boot state (useful in unit tests).
     */
    _resetBootForTests() { this._booted = false; }
    
    
    /**
     * Initialise les références communes au DOM.
     * La lecture de la structure des pages reste à domelements.js.
     */
    setPageRef() {
        /* utiliser byName
        this.main = qs("main"); this.menu = qs("nav#sidebar"); this.footer = qs("footer");
        */
        this.main = byName("main")[0]
        this.menu = byName( "nav", document )[0]
        this.footer = byName("footer" , document )[0]        
        this.footerStatus = qs("#statusBar", this.footer)

        this.themeButton = qs("#themeBtn")
        this.themeButton?.addEventListener("click", () => this.themeSwitch() )
        
        this.fullscreenButton = qs("#fullscreenBtn")
        this.fullscreenButton?.addEventListener( "click", () => this.fullscreenSwitch() )

        // Compatibilité avec les gestionnaires inline de index.html.
        window.openNav = () => this.openNav()
        window.closeNav = () => this.closeNav()
    }

    /**
     * List la structure de la page pricnipalement depuis les titres
     * @param {*} _main reference a `body > main`
     * @param {*} _pc objet PAGE_CONFIG 
     * @returns 
        const PAGE_CONFIG => 
            _pc.selectorPanel           : "article.panel-card" ,
            _pc.offsetPanel             : 2 , //PANEL_OFFSET  due a admin et user
            _pc.classPanelHidden        : "hidden",
            _pc.selectorPanelTitle      : "h2.panel-title",
            _pc.selectorSectionTitle    : "div.section-tab  > section.tab-content > h3",
        }
    */
    
    readPage( _pc ){
    
      let panelSections = null
      let strTemp = null
      let off_index
      let articleObj = null
      //reset  
      this.pages = []
      this.articles = []
      this.currentPanel = null
      this.lastContentPanel = null
   
        if ( this.main && _pc ) { 

            this.articles = qsa( _pc.selectorPanel , this.main )  // on extrait les informations de la pagearticle
        
            this.articles.forEach((  panel , index ) => {
    
                // les 2 panels admin et user sont omis
                if (index >= _pc.offsetPanel ){   
                    // on recherche le panel actif par defaut 0 n'a pas de classe hidden
                    if ( !panel.classList.contains( _pc.classPanelHidden ) ) { 
                        this.currentPanel = index
                        this.lastContentPanel = index 
                    }
    
                    strTemp = qs( _pc.selectorPanelTitle , panel).innerText       //  on recherche le titre h2 du panel p
                    
                    // on recherche les div.section-tab  > div.tab-content > h3 du panel p
                    panelSections = qsa( _pc.selectorSectionTitle , panel )
                    
                    // on construit 1 objet du panel p avec sont titre ets es sections
    
                    off_index = ( index - _pc.offsetPanel )  
                    // les 2 panel admin et user sont omis il faut décaler l'index des page dans _pages
                    //off_index = ( index )
                    articleObj = { off_index , title : strTemp , sections : [] } // constuire un objet
                    
                    // on stocke l'objet dans _pages
                    this.pages.push( articleObj )
                    
                    //  on ajoute les sections a l'objet
                    panelSections.forEach((  section , sindex ) => { 
                        this.pages[ off_index ].sections.push( section.innerText ) 
                    })      
                } 
            })
    
        }
    }

    /** ------------------------------------------------------------------------------------------------------------------
     * @param {*} _pc objet PAGE_CONFIG
     */
    renderPagination( _pc ){
        let buttonTemp = null
        let pagination_buttons = null

        pagination_buttons = qs( _pc.selectorPagination  , document )
  
        this.pages.forEach((  panel , index ) => { 

            buttonTemp = create( 'button', { type: 'button', class: _pc.classPaginationButton , text: panel.title })
            buttonTemp.addEventListener('click', () => this.switchPanel(index + _pc.offsetPanel ) ) 
            pagination_buttons.appendChild( buttonTemp )
        })
    }
    
    /** ------------------------------------------------------------------------------------------------------------------
     * ajoute dans chaque panel la barrre de navigations panel
     * ajoute les buttons pour naviguer entre les sections d'un article, dans chaque articles de main
     * @param {*} _pc objet PAGE_CONFIG
     * 2026-10-15 :
     * - on a structurer les board user et admin comme les autres on peut donc employer this.articles ici 
     * mais pas pour les menus a ajouter
     * 
     */

    renderNavigation( _pc){
    
        let buttonTemp = null
        if (!this.articles) { return }
        
        this.articles.forEach((  panel , index ) => {
            let panel_header = qs( _pc.selectorPanelHeader , this.articles[index] )   // reference sur element article
            // on recherche les div.section-tab  > div.tab-content > h3 du panel p
            let panelSections = qsa( _pc.selectorSectionTitle , panel )
            //  on parcours les sections de  l'article

            panelSections.forEach((  section , sindex ) => { 

                if (sindex === 0 ){ //par defaut le bouton 0 est actif 
                    buttonTemp = create( 'button', { type: 'button', class: _pc.classPanelHeaderButton + ' active', text: section.innerText } )
                }
                else{
                    buttonTemp = create( 'button', { type: 'button', class: _pc.classPanelHeaderButton , text: section.innerText } )
                }
                //switchSection a revoir 
                panel_header.appendChild( buttonTemp )
                buttonTemp.addEventListener('click', () => { this.switchSection(sindex) })

            })  

        })

    }


    /*
    initMenu devient renderMenu, on passe _pc en parametre il sera stocké par le consructeur
    this.pages et this.menu initilaisé par readPage( _pc )
    on utilse this.pages donc + PANEL_OFFSET qui devient _pc.offsetPanel

    style : 
    - nav
    - nav-article , nav-article open
    - nav-header-row  primary-button
    - nav-toggle
    - ul.nav-toc

    structure 
    
    nav
    nav > div.nav-article
    div.nav-header-row primary-button
    button.nav-toggle
    ul.nav-toc

    const PAGE_CONFIG = { 
        selectorPanel           : "article.panel-card" ,
        offsetPanel             : 2 , //PANEL_OFFSET  due a admin et user
        classPanelHidden        : "hidden",
        selectorPanelTitle      : "h2.panel-title",
        selectorSectionTitle    : "div.section-tab  > section.tab-content > h3",
        selectorPagination      : "div.pagination-buttons" ,
        classPaginationButton   : "primary-button switch-tab-btn",
        selectorPanelHeader     : "div.section-tab > div.tab-headers",
        classPanelHeaderButton  : "tab-btn"
    }

    a ajouter dans PAGE_CONFIG
    - classNavArticle nav-article


    */
    renderMenu( _pc ){
        this.pages.forEach((  panel , index ) => {
        
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
                          
                    this.switchPanel(index + _pc.offsetPanel ) // on utilse _pages donc + _pc.offsetPanel
                    this.statusWrite( `Onglet actif : ${this.pages[index].title}` )
                    this.switchSection(sindex)
                    
                    if (window.innerWidth > 768){ menu_panel.classList.remove('open') } else{ closeNav() }           
                })

            })
            menu_panel_item_button.appendChild(menu_panel_item_i) // construit le bouton
            menu_panel_item.appendChild(menu_panel_item_button) // ajoute le bouton
            menu_panel.appendChild(menu_panel_item)
            menu_panel.appendChild(sub_menu)
          
            this.menu.appendChild(menu_panel) //ajoute le menu

            menu_panel.addEventListener('click', () => { console.log(`menu clic panel : ${index}`) ; this.openMenuPanel(index) })
        
            menu_panel.addEventListener('mouseleave', () => {
              console.log(`leave menu : ${index}`)
              if (window.innerWidth > 768){ menu_panel.classList.remove('open') }
            })            

        })

    }


    /*
    // Navigation entre articles
    // Affiche un article identifié par son index dans this.articles. L'index est celui de la liste complète, boards compris.
    Le problème restant est que switchPanel() remet currentSection à 0, mais ne garantit pas que la première section soit effectivement visible.
    CORRIGE 
        this.currentSection = 0;
    *** this.switchSection(0)
        this.onPanelShown(index)
    return true
    */


    switchPanel(index) {
        const article = this.articles[index]

        if (!article) { console.warn(`Article introuvable pour l'index ${index}.`); return false }

        this.articles.forEach( (item , i)  => { item.classList.toggle("hidden", i !== index ) })
        this.currentPanel = index

        if (!["art-admin", "art-user"].includes(article.id)) { this.lastContentPanel = index }

        this.currentSection = 0;
        this.onPanelShown(index);// Le comportement spécifique est délégué à l'application.
        return true;
    }

    /**
     * Point d'extension appelé après l'affichage d'un article.
     */
    onPanelShown(article) {
        // Implémentation facultative dans app-index.js.
    }

    switchAdmin() { return this.switchPanel(0) }//return this.switchPanelById("art-admin");

    switchUser() { return this.switchPanel(1) } // return this.switchPanelById("art-user");
    
    // ============================================================
    // Navigation entre sections
    // 
    // Affiche une section de l'article courant. Les sections sont des éléments section.tab-content.
    
    switchSection(index) {
        if (!this.currentPanel === null) return false;
        const sections = qsa( "div.section-tab > section.tab-content" , this.articles[this.currentPanel] )
        const buttons = qsa( "div.section-tab > div.tab-headers > button.tab-btn" , this.articles[this.currentPanel] )
        const section = sections[index];

        if (!section) { console.warn(`Section introuvable pour l'index ${index}.`); return false}

        sections.forEach( c => c.classList.remove("active") )
        sections[index].classList.add("active")
        
        buttons.forEach( b => b.classList.remove("active") )
        buttons[index].classList.add("active")

        this.currentSection = index;
        this.onSectionShown(this.currentPanel , this.currentSection );
        return true;
    }

    /**
     * Point d'extension : Leaflet, graphiques ou autres composants.
     */
    onSectionShown(art , sec) {
        // Aucun composant spécifique dans le contrat commun.
    }

    // ============================================================
    // Thème et affichage
    // ============================================================

    themeSwitch() {
        this.currentTheme =
            this.currentTheme === "marine" ? "nature" : "marine";

        document.documentElement.dataset.theme = this.currentTheme;

        if (this.themeButton) {
            this.themeButton.textContent =
                this.currentTheme === "marine"
                    ? "Thème nature"
                    : "Thème marine";
        }
    }

    async fullscreenSwitch() {
        try {
            if (!document.fullscreenElement) {
                await document.documentElement.requestFullscreen();
            } else {
                await document.exitFullscreen();
            }
        } catch (error) {
            console.warn("Plein écran indisponible :", error);
        }
    }

    statusWrite(message) {
        if (this.footerStatus) {
            this.footerStatus.textContent = message;
        }
        else{
            console.log(message)
        }
    }

    // ============================================================
    // Menu responsive
    // ============================================================

    openNav() {
        this.menu?.classList.add("open");
    }

    closeNav() {
        this.menu?.classList.remove("open");
    }


    /*  
    _menu :
    - la fonction openMenuPanel devient une méthode
    - la référence initialisée, par setPageRef,  menu devient this.menu
    -   _menu = byName( "nav", document )[0] - this.menu = byName( "nav", document )[0] => ok
    */

    openMenuPanel(index) {
        const menuPanels = qsa('.nav-article', this.menu )
        menuPanels.forEach((menupanel, i) => { menupanel.classList.toggle('open', i === index) })
    }




    // ============================================================
    // Authentification et montage applicatif
    // ============================================================

    mountApplication() {
        // Point d'extension après authentification.
    }

    noAuth() {
        this.statusWrite("Authentification requise.");
    }
}
