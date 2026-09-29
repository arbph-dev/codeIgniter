// assets/js/core/dateUtils.js
// ─────────────────────────────────────────────────────────────────────────────
// CodeIgniter sérialise les champs $dates des Entity en objet JSON :
//
//   { "date": "1890-11-22 00:00:00.000000", "timezone_type": 3, "timezone": "UTC" }
//
// Au lieu d'une chaîne, ce qui provoque [object Object] dans les tables
// et des valeurs vides dans les <input type="date">.
//
// Exports :
//   formatCiDate(val)  → "YYYY-MM-DD" | ''
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Normalise une valeur date issue de l'API CI vers "YYYY-MM-DD" ou ''.
 *
 * Cas couverts :
 *   null / undefined / ''              → ''
 *   "1890-11-22"                       → "1890-11-22"       (déjà normalisé)
 *   "1890-11-22 00:00:00.000000"       → "1890-11-22"       (string CI brut)
 *   { date: "1890-11-22 00:00:00…" }   → "1890-11-22"       (objet CI typique)
 *   Date JS                            → "YYYY-MM-DD"       (objet natif)
 *
 * @param {*} val
 * @returns {string}
 */
export function formatCiDate(val)
{
    if (!val) return ''

    // Objet DateTime CodeIgniter : { date, timezone_type, timezone }
    if (typeof val === 'object' && !(val instanceof Date) && val.date)
        return String(val.date).substring(0, 10)

    // Date JS native
    if (val instanceof Date)
    {
        const y = val.getFullYear()
        const m = String(val.getMonth() + 1).padStart(2, '0')
        const d = String(val.getDate()).padStart(2, '0')
        return `${y}-${m}-${d}`
    }

    // Chaîne — tronque à 10 chars pour couvrir le format "YYYY-MM-DD HH:MM:SS"
    return String(val).substring(0, 10)
}
