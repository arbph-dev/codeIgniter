

// depalce de assets/js/components/three/resources/ResourceRegistry.js
// vers assets/js/core/ResourceRegistry.js

export class ThreeResourceRegistry
{
    static resources = new Map();

    /**
     * Enregistre une ressource.
     */
    static register({
        resource = '',
        component = null
    })
    {
        if (!resource) {
            throw new Error(
                'ResourceRegistry : ressource manquante.'
            );
        }

        if (!component) {
            throw new Error(
                `ResourceRegistry : composant de la ressource '${resource}' manquant.`
            );
        }

        this.resources.set(
            resource,
            component
        );
    }

    /**
     * Retourne une ressource.
     */
    static get({
        resource = ''
    })
    {
        return this.resources.get(resource) ?? null;
    }

    /**
     * Indique si une ressource existe.
     */
    static has({
        resource = ''
    })
    {
        return this.resources.has(resource);
    }

    /**
     * Supprime toutes les ressources.
     */
    static clear()
    {
        this.resources.clear();
    }
}