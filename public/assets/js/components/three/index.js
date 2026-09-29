// assets/js/components/three/index.js
// =============================================================================
// Iter007
//   · root = document était DÉJÀ présent — aucun changement de signature.
//   ~ init() : ajout guard data-threejs-init
//     Sans ce guard, un second appel init(pane) sur un élément déjà initialisé
//     crée un second contexte WebGL + un second canvas dans l'élément.
//     L'attribut est posé APRÈS component.init() réussi.
// =============================================================================

// CORE
//import { bus } from '/assets/js/core/EventBus.js';   // usage futur
import { ComponentFactory }    from '/assets/js/core/ComponentFactory.js';
import { ComponentRegistry }   from '/assets/js/core/ComponentRegistry.js';
import { ResourceRegistry }    from '/assets/js/core/ResourceRegistry.js';

// SHARED/THREE
import { Viewer }              from '/assets/js/shared/three/Viewer.js';
import { ThreeResourceRegistry } from '/assets/js/shared/three/ThreeResourceRegistry.js';
import { CubeResource }        from '/assets/js/shared/three/resources/CubeResource.js';


// ── Enregistrements (module-level : exécutés une seule fois à l'import) ──────

ComponentRegistry.register({ type: 'viewer', component: Viewer });
ThreeResourceRegistry.register({ resource: 'cube', component: CubeResource });


/**
 * Initialise tous les composants Three.js présents dans root.
 *
 * root = document était déjà là — signature inchangée.
 *
 * Iter007 — guard data-threejs-init :
 *   Filtre les éléments déjà initialisés avant de créer le composant.
 *   Évite la création d'un second WebGLRenderer + canvas sur le même élément.
 *
 * @param {Element|Document} root — document (défaut) ou pane ciblé
 */
export function init(root = document)
{
    const elements = [...root.querySelectorAll('.cp_threejs')]
        .filter(el => !el.dataset.threejsInit)   // guard : déjà initialisé

    elements.forEach(element => {

        let options = {}

        try
        {
            options = JSON.parse(element.dataset.options ?? '{}')
        }
        catch (e)
        {
            console.error('[three] options JSON invalides', e)
        }

        const component = ComponentFactory.create({ element, options })
        component.init()

        element.dataset.threejsInit = '1'   // marquer après init réussie
    })

    if (elements.length) {
        console.log(`[three] ${elements.length} composant(s) initialisé(s)`)
    }
}
