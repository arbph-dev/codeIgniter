// assets/js/ui/workbench/personne/PersonneListPanel.js
import ListPanelBase       from '/assets/js/ui/workbench/core/ListPanelBase.js'
import { table }           from '/assets/js/core/domhelper.js'
import { formatCiDate }    from '/assets/js/core/dateUtils.js'

export class PersonneListPanel extends ListPanelBase
{
    constructor()
    {
        super({
            title             : 'Personnes',
            newLabel          : 'Nouveau',
            searchPlaceholder : 'Nom, prénom…',
            pagerStyle        : 'compact',
            pagerMaxVisible   : 5,
        })
    }

    _renderRows(items)
    {
        // Les dates CI arrivent sous forme d'objet { date, timezone_type, timezone }.
        // On les normalise en "YYYY-MM-DD" avant le rendu pour éviter [object Object].
        const rows = items.map(p => ({
            ...p,
            date_naissance : formatCiDate(p.date_naissance),
        }))

        this.tableEl.appendChild(
            table({
                data       : rows,
                columns    : [
                    { key: 'id',             label: 'ID'       },
                    { key: 'nom_complet',    label: 'Nom'      },
                    { key: 'date_naissance', label: 'Né(e) le' },
                ],
                attrs      : { class: this.styles.table },
                onRowClick : (row) => this._onSelectFn?.(row),
            })
        )
    }
}

export default PersonneListPanel
