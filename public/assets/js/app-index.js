// /assets/js/app-index.js.js
/**
 * Étend UiBase
 * implémente boot() et onSectionShown(), puis branche l’authentification et les composants.
 */
"use strict"

import { UiBase } from '/assets/js/uibase.js'

let APP

class AppIndex extends UiBase {
    constructor() {
        super()
        this.setPageRef()
    }

    boot() {
        console.info("AppIndex :: boot() = success")
    }
    onPanelShown(article) {
        console.info("AppIndex :: onPanelShown() = article")
        console.table(article)
    }
}





document.addEventListener("DOMContentLoaded", () => { 
    APP = new AppIndex()

 }) 

// onload 
window.onload = (event) => {

    APP.boot()
} 
