//      /assets/js/shared/three/resources/ResourceLoader.js 

// deplacer de  assets/js/components/three/resources/ResourceLoader.js 
// vers /assets/js/shared/three/resources/ResourceLoader.js 


// ResourceRegistry a modifier en ThreeResourceRegistry
import { ThreeResourceRegistry } from '/assets/js/shared/three/ThreeResourceRegistry.js';


export class ResourceLoader
{
    /**
     * Charge une ressource.
     *
     * @param {Object} params
     * @param {string} params.resource
     * @param {Viewer} params.viewer
     * @returns {Object}
     */
    static load( { resource = '' , viewer = null } )
    {
        if (!resource)
        {
            throw new Error( 'ResourceLoader : ressource manquante.' );
        }

        if (!viewer)
        {
            throw new Error( 'ResourceLoader : viewer manquant.' );
        }

        //modifier en ThreeResourceRegistry
        const Resource = ThreeResourceRegistry.get({
            resource
        });

        if (!Resource)
        {
            throw new Error(
                `ResourceLoader : ressource '${resource}' non enregistrée.`
            );
        }

        return new Resource({

            viewer,

            options: viewer.options.resourceOptions ?? {}

        });
    }
}
