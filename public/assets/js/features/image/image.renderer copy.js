// js/features/image/image.renderer.js

import { bus } from '../../core/eventBus.js'

import {
    create,
    table,
    clear,
    btn,
    detail,
    pagination,
    notice
}
from '/assets/js/core/domhelper.js'

// ─────────────────────────────────────────────────────────────
// BUTTONS
// ─────────────────────────────────────────────────────────────

function buildButtons(selected) {

    const frag = document.createDocumentFragment()

    frag.append(

        btn({
            label: 'Rechercher',
            icon: 'fa-search',
            busEvent: 'image:mode',
            busArg: 'list'
        }),

        btn({
            label: 'Nouvelle',
            icon: 'fa-plus',
            variant: 'primary',
            busEvent: 'image:mode',
            busArg: 'form'
        }),

        btn({
            label: 'Modifier',
            icon: 'fa-pencil',
            busEvent: 'image:mode',
            busArg: 'form',
            disabled: !selected
        }),

        btn({
            label: 'Supprimer',
            icon: 'fa-trash',
            variant: 'danger',
            busEvent: 'image:mode',
            busArg: 'delete',
            disabled: !selected
        })
    )

    return frag
}

// ─────────────────────────────────────────────────────────────
// SEARCH FORM
// ─────────────────────────────────────────────────────────────

function buildSearchForm() {

    const form = create('form', {
        id: 'imageSearchForm',
        onsubmit: 'return validateForm(this)'
    })

    form.append(

        create('label', {
            text: 'Recherche'
        }),

        create('input', {
            type: 'text',
            name: 'imageq'
        }),

        create('label', {
            text: 'Statut'
        }),

        create('select', {
            name: 'imagestatus'
        }),

        btn({
            label: 'Rechercher',
            icon: 'fa-search',
            variant: 'primary',
            attrs: { type: 'submit' }
        })
    )

    return form
}

// ─────────────────────────────────────────────────────────────
// DETAIL
// ─────────────────────────────────────────────────────────────

function buildDetail(selected) {

    if (!selected) {
        return document.createDocumentFragment()
    }

    return detail([
        { label: 'ID', value: selected.id },
        { label: 'Fichier', value: selected.filename },
        { label: 'Alt', value: selected.alt },
        { label: 'Status', value: selected.status },
        { label: 'Dimensions', value: `${selected.width} × ${selected.height}` },
    ])
}

// ─────────────────────────────────────────────────────────────
// INIT
// ─────────────────────────────────────────────────────────────

export function initImageRenderer() {

    const container =
        document.getElementById('imageContainer')

    if (!container) {
        return
    }

    const panels = {

        buttons:
            container.querySelector('.cp_panel_buttons'),

        form:
            container.querySelector('.cp_panel_form'),

        detail:
            container.querySelector('.cp_panel_detail'),

        table:
            container.querySelector('.cp_panel_table'),

        pagination:
            container.querySelector('.cp_panel_pagination'),
    }

    function applyMode(store) {

        const {
            mode,
            selected,
            data,
            pagination: pager
        } = store

        clear(panels.buttons)

        panels.buttons.appendChild(
            buildButtons(selected)
        )

        if (mode === 'list') {

            clear(panels.form)

            panels.form.appendChild(
                buildSearchForm()
            )
        }

        if (mode === 'detail') {

            clear(panels.detail)

            panels.detail.appendChild(
                buildDetail(selected)
            )
        }

        if (mode !== 'list') {
            return
        }

        clear(panels.table)

        if (!data?.length) {

            panels.table.appendChild(
                notice('empty')
            )

            return
        }

        panels.table.appendChild(

            table({
                id: 'imageTable',

                data,

                columns: [

                    { key: 'id', label: 'ID' },

                    { key: 'filename', label: 'Fichier' },

                    { key: 'status', label: 'Statut' },

                    { key: 'width', label: 'W' },

                    { key: 'height', label: 'H' },
                ],

                attrs: {
                    class: 'cp_table'
                },

                onRowClick: (row) =>
                    bus.publish('image:select', row)
            })
        )

        if (pager) {

            clear(panels.pagination)

            panels.pagination.appendChild(

                pagination({
                    pager,
                    busEvent: 'image:page',
                    style: 'compact',
                    maxVisible: 5,
                })
            )
        }
    }

    bus.subscribe('image:render', applyMode)

    bus.subscribe('image:loading', (loading) => {

        if (!loading) return

        clear(panels.table)

        panels.table.appendChild(
            notice('loading')
        )
    })

    bus.subscribe('image:error', (msg) => {

        clear(panels.table)

        panels.table.appendChild(
            notice('error', msg)
        )
    })

    bus.publish('image:render', {
        mode: 'list',
        selected: null,
        data: [],
        pagination: null
    })
}