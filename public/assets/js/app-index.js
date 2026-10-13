// /assets/js/app-index.js.js
/**
 * Étend UiBase
 * implémente boot() et onSectionShown(), puis branche l’authentification et les composants.
 * 
 * 2026-10-12 voir ligne 204 /assets/js/uibase.js + ligne 90 /assets/js/app-index.js.js
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
        bus.subscribe('auth:success', () => {
            this.mountApplication()
            this.mountUserBoard(authStore.user)
            this.mountAdminBoard(authStore.user)
        })


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
    

    // ============================================================
    // 2026-10-12
    // Montage applicatif après authentification

    async mountApplication() {
        console.log('Auth success; app can run')
    }

    // ============================================================
    // Badges du profil

    badgeGroups(groups) {
        if (!groups?.length) {
            return '<span class="adm-badge adm-badge--none">—</span>'
        }

        return groups.map(g =>
            `<span class="adm-badge adm-badge--${g}">${g}</span>`
        ).join(' ')
    }

    badgePerms(permissions) {
        if (!permissions?.length) {
            return '<span class="adm-badge adm-badge--none">—</span>'
        }

        return permissions.map(p =>
            `<span class="adm-badge">${p}</span>`
        ).join(' ')
    }

    // ============================================================
    // Carte utilisateur

    renderUserCard(user, { title = 'Mon profil' } = {}) {
        const initial = (user?.username?.[0] ?? '?').toUpperCase()

        return `
            <div class="adm-detail-header">
                <div class="adm-avatar">${initial}</div>
                <div>
                    <h3>${title}</h3>
                    <p>${user?.username ?? '—'} · ${user?.email ?? '—'}</p>
                </div>
            </div>

            <dl class="adm-dl">
                <dt>ID</dt>
                <dd>${user?.id ?? '—'}</dd>

                <dt>Username</dt>
                <dd>${user?.username ?? '—'}</dd>

                <dt>Email</dt>
                <dd>${user?.email ?? '—'}</dd>

                <dt>Groupes</dt>
                <dd>${this.badgeGroups(user?.groups)}</dd>

                <dt>Permissions</dt>
                <dd>${this.badgePerms(user?.permissions)}</dd>
            </dl>

            <p class="panel-hint">
                <em>Profils (user_profils) — à venir après seeder + tests register</em>
            </p>
        `
    }

    // ============================================================
    // Tableau de bord utilisateur

    mountUserBoard(user) {
        const body = document.querySelector('#art-user-home-body')
        if (!body) return

        body.innerHTML = this.renderUserCard(user, {
            title: 'Mon espace'
        })
    }

    // ============================================================
    // Tableau de bord administration

    mountAdminBoard(user) {
        const body = document.querySelector('#art-admin-home-body')
        if (!body) return

        const isAdmin = (user?.groups ?? []).some(g =>
            ['admin', 'superadmin'].includes(g)
        )

        body.innerHTML = `
            ${this.renderUserCard(user, { title: 'Administration' })}
            <hr>
            <p>${isAdmin
                ? 'Accès admin OK. Gestion utilisateurs / seeder — prochaine étape.'
                : 'Accès refusé — groupe admin requis.'
            }</p>
        `
    }    
}





document.addEventListener("DOMContentLoaded", () => { 
    APP = new AppIndex()

 }) 

// onload 
window.onload = (event) => {

    APP.boot()
} 
