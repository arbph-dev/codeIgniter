// /assets/js/admin/bootstrap.js
import { bus } from '/assets/js/core/eventBus.js'


import { initApex } from '/assets/js/components/apex.js'
import { initCallout} from '/assets/js/components/callout.js'
import { initCodeVal } from '/assets/js/components/codeval.js'
import { initLeaflet }  from '/assets/js/components/leaflet.js'
import { initMermaid } from '/assets/js/components/mermaid.js'
import { initWysedit } from '/assets/js/ihm/wysedit.js'
import { init } from '/assets/js/components/three/index.js'



window.eventBusPublish = (evt, eventName, page) => {

    bus.publish(eventName, page)

}

window.adminRenderMermaid = function(id)
{
    const source = document.getElementById( `MERMAID_${id}_SOURCE`);
    const result = document.getElementById( `MERMAID_${id}_RESULT`);

    if (!source || !result) { return; }

    result.textContent = source.value;
    
    window.eventBusPublish( null, 'mermaid:render', { id: result.id } );
}

export function initAdmin()
{
    initWysedit()

    initCodeVal()

    initApex()

    initMermaid()
}
