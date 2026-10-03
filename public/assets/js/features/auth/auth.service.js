// js/features/auth/auth.service.js

const BASE = '/api/auth'

function authHeaders(token = null) {
    const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' }
    if (token) headers['Authorization'] = `Bearer ${token}`
    return headers
}

// ── POST /api/auth/login ─────────────────────────────────────────────────────

export async function fetchLogin({ email, password }) {
    const res = await fetch(`${BASE}/login`, {
        method:  'POST',
        headers: authHeaders(),
        body:    JSON.stringify({ email, password }),
    })

    const data = await res.json()

    if (!res.ok) {
        // 422 validation, 401 credentials invalides
        const msg = data.errors
            ? Object.values(data.errors).join(' ')
            : (data.error ?? `HTTP ${res.status}`)
        throw new Error(msg)
    }

    return data // { token, user: { id, username, email, groups, permissions } }
}

// ── POST /api/auth/register ──────────────────────────────────────────────────
//
// payload attendu :
// {
//   shield_username, shield_email, shield_password,
// }
//
// Réponses possibles :
//   200 { message, email_verified: false }          → EmailActivator actif
//   201 { message, email_verified: true, token, user } → pas d'activation
//   422 { errors: {...} }                           → validation
//   409 { error: '...' }                            → conflit profil               OBSOLETE ?
//   500 { error: '...' }

export async function fetchRegister(payload) {
    const res = await fetch(`${BASE}/register`, {
        method:  'POST',
        headers: authHeaders(),
        body:    JSON.stringify(payload),
    })

    const data = await res.json()

    if (!res.ok) {
        const msg = data.errors
            ? Object.values(data.errors).flat().join(' ')
            : (data.error ?? `HTTP ${res.status}`)
        throw new Error(msg)
    }

    return data
}



// ── GET /api/auth/me ─────────────────────────────────────────────────────────
// Accepte session Shield OU Bearer token

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
// ── POST /auth/a/verify ──────────────────────────────────────────────────────
// Activation native Shield — EmailActivator
//
// Le code d'activation est envoyé comme champ "token".
// Cette route utilise la session Shield créée lors du register.
//
// IMPORTANT :
// - pas de Bearer token
// - pas de JSON
// - Shield peut répondre par une redirection

export async function fetchActivate(token) {
    const res = await fetch('/auth/a/verify', {
        method: 'POST',
        headers: {
            'Accept': 'text/html, application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ token }),
        redirect: 'manual',
    })

    // Shield renvoie une redirection après une activation réussie.
    // Avec redirect: 'manual', le navigateur expose normalement
    // cette réponse comme "opaqueredirect".
    if (res.type === 'opaqueredirect' || (res.status >= 300 && res.status < 400)) {
        return {
            success: true,
            message: 'Compte activé. Vous pouvez maintenant vous connecter.',
        }
    }

    // Un code invalide provoque le retour de la vue d'activation
    // avec HTTP 200. Ce n'est donc PAS un succès.
    throw new Error(
        'Code d’activation invalide ou expiré.'
    )
}
