// assets/js/features/typevoie/typevoie.service.js
// ─────────────────────────────────────────────────────────────────────────────
// CRUD complet (contrairement à CodePostal qui est read-only).
//
// Adapté depuis old/typevoie.service.js :
//   - apiFetch depuis le chemin architecture new
//   - fetchTvLike retourne items[] directement (compatible RelationPickerDialog)
//   - fetchTv : param id retiré -> fetchTvById couvre ce cas
//   - saveTv  : pattern POST/PUT aligné sur image.service.js
// ─────────────────────────────────────────────────────────────────────────────

import { apiFetch } from '/assets/js/core/apiFetch.js'

/**
 * Recherche paginée.
 *
 * @param {object} params
 * @param {string} [params.q]
 * @param {number} [params.page]
 * @param {number} [params.perPage]
 * @returns {Promise<{ data: object[], pager: object }>}
 */
export async function fetchTv({ q, page = 1, perPage = 20 } = {})
{
    const params = new URLSearchParams()
    if (q)       params.set('q',        q)
    if (page)    params.set('page',     page)
    if (perPage) params.set('per_page', perPage)

    const res = await apiFetch(`/api/typevoie?${params}`)
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    return res.json()
}

/**
 * Charge un type de voie par ID.
 *
 * @param {number} id
 * @returns {Promise<{ data: object }>}
 */
export async function fetchTvById(id)
{
    const res = await apiFetch(`/api/typevoie/${id}`)
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    return res.json()
}

/**
 * Recherche rapide pour autocomplete / RelationPickerDialog.
 * Retourne un tableau plat d'items (pas de pager).
 *
 * Chaque item contient au minimum : { id, nom }
 *
 * @param {object} params
 * @param {string} params.q
 * @param {number} [params.len]
 * @returns {Promise<object[]>}
 */
export async function fetchTvLike({ q = '', len = 10 } = {})
{
    if (!q) return []

    try
    {
        const params = new URLSearchParams({ q, len })
        const res    = await apiFetch(`/api/typevoie/like?${params}`)
        const json   = await res.json()
        return json.data ?? json ?? []
    }
    catch
    {
        return []
    }
}

/**
 * Crée (POST) ou met à jour (PUT) un type de voie.
 * JSON pur.
 *
 * @param {object}      params
 * @param {number|null} [params.id]
 * @param {string}      params.nom
 * @returns {Promise<{ data: object }>}
 */
export async function saveTv({ id = null, nom = '' } = {})
{
    const method = id ? 'PUT' : 'POST'
    const url    = id ? `/api/typevoie/${id}` : '/api/typevoie'

    const res = await apiFetch(url, {
        method,
        body : JSON.stringify({ nom }),
    })

    if (!res.ok)
    {
        const err = await res.json().catch(() => ({}))
        throw new Error(err.message ?? `HTTP ${res.status}`)
    }

    return res.json()
}

/**
 * Supprime un type de voie.
 *
 * @param {number} id
 * @returns {Promise<object>}
 */
export async function deleteTv(id)
{
    const res = await apiFetch(`/api/typevoie/${id}`, { method: 'DELETE' })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    return res.json()
}
