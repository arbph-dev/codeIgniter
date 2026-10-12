// /assets/js/app-index.js.js
/**
 * Étend UiBase
 * implémente boot() et onSectionShown(), puis branche l’authentification et les composants.
 */
"use strict"

import { UiBase } from '/assets/js/uibase.js'
import { initApex } from '/assets/js/components/apex.js'
import { initCodeVal } from '/assets/js/components/codeval.js'
import { initCallout} from '/assets/js/components/callout.js'

//ajout de auth
import { bus } from '/assets/js/core/eventBus.js'

import { initAuthController } from '/assets/js/features/auth/auth.controller.js'
import { authStore } from '/assets/js/features/auth/auth.store.js'
import ToolbarAuthPanel       from '/assets/js/ui/workbench/auth/ToolbarAuthPanel.js'


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

let APP

class AppIndex extends UiBase {
    /**
     * this.setPageRef() initiliase les références voir UiBase
     * - this.main -> html > body > main 
     * this.readPage(PAGE_CONFIG) lit la strucutre de la page
    */
    constructor() {
        super()
        this.setPageRef() 
        this.readPage(PAGE_CONFIG) 
        console.table(this.pages)
        this.renderPagination(PAGE_CONFIG)
        this.renderNavigation(PAGE_CONFIG)
    }

    boot() {
        console.info("AppIndex :: boot() = success")
        initApex()
        initCodeVal()
        initCallout()


        initAuthController() // /public/assets/js/features/auth/auth.controller.js
        new ToolbarAuthPanel().init() 

        bus.subscribe('auth:changed', () => { if (authStore.loggedIn) this.switchUser() } )   // login / register avec connexion immédiate / logout
        bus.subscribe('auth:loading', () => this.statusWrite('auth:loading') )
        bus.subscribe('auth:guest',   () => this.noAuth())
        
        // [X] Separer la creation de l'affichage - mountAdminBoard(authStore.user) dans bus.subscribe('auth:success') ligne 515
        bus.subscribe( 'auth:success', () => { 
            mountApplication() ; mountUserBoard(authStore.user) ; mountAdminBoard(authStore.user) 
        } )


        // ── Affichage des boards ──
        // [X] Separer la creation de l'affichage     switchAdmin()
        bus.subscribe('board:admin',    () => { this.switchAdmin() })
        bus.subscribe('board:hide',     () => this.switchPanel(this.lastContentPanel) )
        bus.subscribe('board:user',     () => this.switchUser() )
        bus.subscribe('board:register', () => this.switchUser() )
        
        bus.publish('auth:check')



    }
    onPanelShown(artIdx) { console.info("AppIndex :: onPanelShown() = artIdx " + artIdx) }

    onSectionShown(artIdx , secIdx){ console.info("AppIndex :: onSectionShown() = artIdx " + artIdx + " secIdx " + secIdx ) }     
    
}





document.addEventListener("DOMContentLoaded", () => { 
    APP = new AppIndex()

 }) 

// onload 
window.onload = (event) => {

    APP.boot()
} 
