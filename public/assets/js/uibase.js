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
 */

"use strict";

/*import { qs } from "/assets/js/core/domhelper.js";*/
import { byId, byName , qs , qsa , create } from '/assets/js/core/domhelper.js'

export class UiBase {

    constructor() {
        // Références DOM communes
        this.main = null;
        this.menu = null;
        this.footer = null;
        this.footerStatus = null;
        this.themeButton = null;
        this.fullscreenButton = null;

        // Structure enregistrée par domelements.js
        this.articles = [];
        this.currentPanel = null;
        this.currentSection = 0;
        this.lastContentPanel = null;

        this.currentTheme = "marine";
    }

    // ============================================================
    // Cycle de vie
    // ============================================================

    /**
     * Point d'entrée obligatoire de l'application concrète.
     * Doit être implémenté dans app-index.js.
     */
    boot() {
        throw new Error("UiBase.boot() doit être implémentée.");
    }

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
        this.fullscreenButton = qs("#fullscreenBtn")

        this.themeButton?.addEventListener("click", () => this.themeSwitch() )

        this.fullscreenButton?.addEventListener( "click", () => this.fullscreenSwitch() )

        // Compatibilité avec les gestionnaires inline de index.html.
        window.openNav = () => this.openNav()
        window.closeNav = () => this.closeNav()
    }

    /**
     * Enregistre les articles identifiés par domelements.js.
     * Les articles doivent être fournis dans l'ordre du document.
     */
    setArticles(articles) {
        this.articles = Array.from(articles ?? []);

        this.currentPanel =
            this.articles.find(article =>
                !article.classList.contains("hidden")
            ) ?? null;

        this.lastContentPanel =
            this.articles.find(article =>
                !["art-admin", "art-user"].includes(article.id)
                && !article.classList.contains("hidden")
            ) ?? this.articles.find(article =>
                !["art-admin", "art-user"].includes(article.id)
            ) ?? null;
    }

    // ============================================================
    // Navigation entre articles
    // ============================================================

    /**
     * Affiche un article identifié par son index dans this.articles.
     * L'index est celui de la liste complète, boards compris.
     */
    switchPanel(index) {
        const article = this.articles[index];

        if (!article) {
            console.warn(`Article introuvable pour l'index ${index}.`);
            return false;
        }

        this.articles.forEach(item => {
            item.classList.toggle("hidden", item !== article);
        });

        this.currentPanel = article;

        if (!["art-admin", "art-user"].includes(article.id)) {
            this.lastContentPanel = article;
        }

        this.currentSection = 0;

        // Le comportement spécifique est délégué à l'application.
        this.onPanelShown(article);

        return true;
    }

    /**
     * Point d'extension appelé après l'affichage d'un article.
     */
    onPanelShown(article) {
        // Implémentation facultative dans app-index.js.
    }

    switchAdmin() {
        return this.switchPanelById("art-admin");
    }

    switchUser() {
        return this.switchPanelById("art-user");
    }

    switchPanelById(id) {
        const index = this.articles.findIndex(article => article.id === id);

        if (index < 0) {
            console.warn(`Article "${id}" introuvable.`);
            return false;
        }

        return this.switchPanel(index);
    }

    /**
     * Retourne au dernier article de contenu.
     */
    switchContent() {
        if (!this.lastContentPanel) return false;

        const index = this.articles.indexOf(this.lastContentPanel);

        return index >= 0 ? this.switchPanel(index) : false;
    }

    // ============================================================
    // Navigation entre sections
    // ============================================================

    /**
     * Affiche une section de l'article courant.
     * Les sections sont des éléments section.tab-content.
     */
    switchSection(index) {
        if (!this.currentPanel) return false;

        const sections = this.currentPanel.querySelectorAll(
            ".section-tab > section.tab-content"
        );

        const buttons = this.currentPanel.querySelectorAll(
            ".section-tab > .tab-headers > button.tab-btn"
        );

        const section = sections[index];

        if (!section) {
            console.warn(`Section introuvable pour l'index ${index}.`);
            return false;
        }

        sections.forEach((item, i) => {
            item.classList.toggle("active", i === index);
        });

        buttons.forEach((button, i) => {
            button.classList.toggle("active", i === index);
            button.setAttribute("aria-selected", String(i === index));
        });

        this.currentSection = index;
        this.onSectionShown(section);

        return true;
    }

    /**
     * Point d'extension : Leaflet, graphiques ou autres composants.
     */
    onSectionShown(section) {
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
