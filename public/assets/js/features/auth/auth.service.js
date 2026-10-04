// js/features/auth/auth.service.js

const BASE = '/api/auth'

function authHeaders(token = null) {
    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' }
    if (token) headers['Authorization'] = `Bearer ${token}`
    return headers
}

/**
 * Message d'erreur lisible depuis une réponse JSON d'erreur :
 *   { errors: { champ: msg | [msgs] } }  ou  { error: msg }
 */
function errorMessage(data, res) {
    if (data?.errors) return Object.values(data.errors).flat().join(' ')
    return data?.error ?? `HTTP ${res.status}`
}

/**
 * Lecture JSON tolérante : un 404/500 HTML ne doit pas produire
 * une SyntaxError incompréhensible.
 */
async function readJson(res) {
    try {
        return await res.json()
    } catch {
        return null
    }
}

// ── POST /api/auth/login ─────────────────────────────────────────────────────
// 200 { token, email_verified: true, user }
// 401 { error }                                  → credentials invalides
// 403 { error, email_verified: false }           → compte non activé
// 422 { errors }                                 → validation

export async function fetchLogin({ email, password }) {
    const res  = await fetch(`${BASE}/login`, {
        method:  'POST',
        headers: authHeaders(),
        body:    JSON.stringify({ email, password }),
    })
    const data = await readJson(res)

    if (!res.ok) {
        const err = new Error(errorMessage(data, res))
        err.status        = res.status
        err.emailVerified = data?.email_verified   // false si compte non activé
        throw err
    }

    return data
}

// ── POST /api/auth/register ──────────────────────────────────────────────────
// payload : { shield_username, shield_email, shield_password }
//
//   201 { message, email_verified: false }            → EmailActivator actif
//   201 { message, email_verified: true, user }       → pas d'activation
//   422 { errors }                                    → validation
//   500 { error }

export async function fetchRegister(payload) {
    const res  = await fetch(`${BASE}/register`, {
        method:  'POST',
        headers: authHeaders(),
        body:    JSON.stringify(payload),
    })
    const data = await readJson(res)

    if (!res.ok) throw new Error(errorMessage(data, res))

    return data
}

// ── POST /api/auth/activate ──────────────────────────────────────────────────
// Remplace /auth/a/verify (qui exige la session Shield de l'inscription
// et répond 404 après fermeture du navigateur).
//
//   200 { message }            → compte activé (ou déjà activé)
//   422 { error | errors }     → code invalide / validation
//   429 { error }              → trop d'essais

export async function fetchActivate({ email, token }) {
    const res  = await fetch(`${BASE}/activate`, {
        method:  'POST',
        headers: authHeaders(),
        body:    JSON.stringify({ email, token }),
    })
    const data = await readJson(res)

    if (!res.ok) throw new Error(errorMessage(data, res))

    return data
}

// ── POST /api/auth/resend ────────────────────────────────────────────────────
// Régénère le code d'activation et renvoie l'email.
//
//   200 { message }   → réponse identique que le compte existe ou non
//   429 { error }     → trop de demandes

export async function fetchResend(email) {
    const res  = await fetch(`${BASE}/resend`, {
        method:  'POST',
        headers: authHeaders(),
        body:    JSON.stringify({ email }),
    })
    const data = await readJson(res)

    if (!res.ok) throw new Error(errorMessage(data, res))

    return data
}

// ── GET /api/auth/me ─────────────────────────────────────────────────────────
// Bearer token uniquement (le filtre "tokens" protège cette route).

export async function fetchMe(token = null) {
    const res = await fetch(`${BASE}/me`, {
        headers: authHeaders(token),
    })

    if (res.status === 401) return null  // non connecté — pas une erreur

    if (!res.ok) throw new Error(`HTTP ${res.status}`)

    return await res.json() // { id, username, email, groups, permissions }
}

// ── POST /api/auth/logout ────────────────────────────────────────────────────

export async function fetchLogout(token) {
    const res = await fetch(`${BASE}/logout`, {
        method:  'POST',
        headers: authHeaders(token),
    })

    if (!res.ok) throw new Error(`HTTP ${res.status}`)

    return await res.json()
}
