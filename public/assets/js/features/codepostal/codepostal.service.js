// assets/js/features/codepostal/codepostal.service.js
// ─────────────────────────────────────────────────────────────────────────────
// Référentiel read-only — pas de save ni delete.
//
// Adapté depuis old/codepostal.service.js :
//   - apiFetch depuis le chemin architecture new
//   - fetchCpLike retourne items[] directement (pas { data:[] })
//     => compatible RelationPickerDialog.fetchFn
//   - fetchCp : paramètres q / codepostal / codeinsee conservés
// ─────────────────────────────────────────────────────────────────────────────

import { apiFetch } from '/assets/js/core/apiFetch.js'

/**
 * Recherche paginée avec filtres multiples.
 *
 * @param {object}  params
 * @param {string}  [params.q]          Recherche texte libre (commune, voie…)
 * @param {string}  [params.codepostal] Filtre exact sur le code postal
 * @param {string}  [params.codeinsee]  Filtre exact sur le code INSEE
 * @param {number}  [params.page]
 * @param {number}  [params.perPage]
 * @returns {Promise<{ data: object[], pager: object }>}
 */
export async function fetchCp({ q, codepostal, codeinsee, page = 1, perPage = 20 } = {})
{
    const params = new URLSearchParams()
    if (q)          params.set('q',          q)
    if (codepostal) params.set('codepostal', codepostal)
    if (codeinsee)  params.set('codeinsee',  codeinsee)
    if (page)       params.set('page',       page)
    if (perPage)    params.set('per_page',   perPage)

    const res = await apiFetch(`/api/codepostal?${params}`)
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    return res.json()
}

/**
 * Recherche rapide pour autocomplete / RelationPickerDialog.
 * Retourne un tableau plat d'items (pas de pager).
 *
 * Chaque item contient au minimum : { id, codepostal, commune }
 *
 * @param {object} params
 * @param {string} params.q
 * @param {number} [params.len]
 * @returns {Promise<object[]>}
 */
export async function fetchCpLike({ q = '', len = 10 } = {})
{
    if (!q || q.length < 2) return []

    try
    {
        const params = new URLSearchParams({ q, len })
        const res    = await apiFetch(`/api/codepostal/like?${params}`)
        const json   = await res.json()
        return json.data ?? json ?? []
    }
    catch
    {
        return []
    }
}
