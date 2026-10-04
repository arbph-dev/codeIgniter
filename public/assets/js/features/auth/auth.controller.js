// js/features/auth/auth.controller.js

import { bus }                          from '../../core/eventBus.js'
import { authStore }                    from './auth.store.js'
import {
    fetchLogin, fetchMe, fetchLogout,
    fetchRegister, fetchActivate, fetchResend,
} from './auth.service.js'

/*
auth:check              vérifie si un token valide existe (GET /api/auth/me)
auth:login              seul endroit qui récupère le Personal Access Token
auth:register           crée le compte
auth:register:pending   compte non activé : afficher message + saisie du code
auth:activate           POST /api/auth/activate { email, token }
auth:activated          compte activé (aucun token créé, l'utilisateur doit se connecter)
auth:resend             POST /api/auth/resend { email } → nouveau code par email
auth:success            l'application dispose d'une authentification exploitable
auth:changed            login / register avec connexion immédiate / logout
auth:logout

authStore.pendingEmail  email du compte en attente d'activation.
                        Renseigné par auth:register et par auth:login (403 non activé),
                        conservé en mémoire seulement (jamais le mot de passe).
*/

export function initAuthController() {

    // ── auth:check ───────────────────────────────────────────────────────────
    // Appelé au démarrage
    bus.subscribe('auth:check', async () => {
        authStore.restore()              // récupère token/user depuis sessionStorage
        authStore.loading = true
        bus.publish('auth:loading', true)

        try {
            const user = await fetchMe(authStore.token)

            if (user) {
                authStore.user     = user
                authStore.loggedIn = true
                authStore.persist()
                bus.publish('auth:success', { user, token: authStore.token })
            } else {
                authStore.clear()
                bus.publish('auth:guest')
            }
        } catch (err) {
            authStore.clear()
            bus.publish('auth:guest')
        } finally {
            authStore.loading = false
            bus.publish('auth:loading', false)
        }
    })

    // ── auth:login ───────────────────────────────────────────────────────────
    bus.subscribe('auth:login', async ({ email, password }) => {
        authStore.loading = true
        authStore.error   = null
        bus.publish('auth:loading', true)

        try {
            const data = await fetchLogin({ email, password })

            authStore.user         = data.user
            authStore.token        = data.token
            authStore.loggedIn     = true
            authStore.pendingEmail = null
            authStore.persist()

            bus.publish('auth:success', { user: data.user, token: data.token })
            bus.publish('auth:changed')

        } catch (err) {
            if (err.emailVerified === false) {
                // Compte créé mais non activé → même écran qu'après l'inscription
                authStore.pendingEmail = email
                bus.publish('auth:register:pending', { message: err.message })
            } else {
                authStore.error = err.message
                bus.publish('auth:error', err.message)
            }
        } finally {
            authStore.loading = false
            bus.publish('auth:loading', false)
        }
    })

    // ── auth:register ────────────────────────────────────────────────────────
    // payload : { shield_username, shield_email, shield_password }
    bus.subscribe('auth:register', async (payload) => {
        authStore.loading = true
        authStore.error   = null
        bus.publish('auth:loading', true)

        try {
            const data = await fetchRegister(payload)

            // EmailActivator actif — pas de token, compte non activé
            if (data.email_verified === false) {
                authStore.pendingEmail = payload.shield_email
                bus.publish('auth:register:pending', {
                    message: data.message ?? 'Compte créé. Vérifiez votre email pour activer votre compte.',
                })
                return
            }

            // Sans activation — login immédiat
            if (data.token && data.user) {
                authStore.user     = data.user
                authStore.token    = data.token
                authStore.loggedIn = true
                authStore.persist()

                bus.publish('auth:success', { user: data.user, token: data.token })
                bus.publish('auth:changed')
                return
            }

            // Fallback — succès sans token ni pending explicite
            bus.publish('auth:register:pending', {
                message: data.message ?? 'Compte créé.',
            })

        } catch (err) {
            authStore.error = err.message
            bus.publish('auth:error', err.message)
        } finally {
            authStore.loading = false
            bus.publish('auth:loading', false)
        }
    })

    // ── auth:activate ────────────────────────────────────────────────────────
    // Vérification du code envoyé par email.
    // L'activation ne crée pas de token et ne connecte pas l'utilisateur.
    bus.subscribe('auth:activate', async ({ token }) => {
        authStore.loading = true
        authStore.error   = null
        bus.publish('auth:loading', true)

        try {
            if (!authStore.pendingEmail) {
                throw new Error('Email inconnu : reconnectez-vous pour recevoir le formulaire d’activation.')
            }

            const data = await fetchActivate({ email: authStore.pendingEmail, token })

            authStore.pendingEmail = null
            bus.publish('auth:activated', data)

        } catch (err) {
            authStore.error = err.message
            bus.publish('auth:error', err.message)

        } finally {
            authStore.loading = false
            bus.publish('auth:loading', false)
        }
    })

    // ── auth:resend ──────────────────────────────────────────────────────────
    // Nouveau code d'activation. On reste dans l'écran "pending".
    bus.subscribe('auth:resend', async () => {
        authStore.loading = true
        authStore.error   = null
        bus.publish('auth:loading', true)

        try {
            if (!authStore.pendingEmail) {
                throw new Error('Email inconnu : reconnectez-vous pour recevoir le formulaire d’activation.')
            }

            const data = await fetchResend(authStore.pendingEmail)

            bus.publish('auth:register:pending', {
                message: data.message ?? 'Un nouveau code a été envoyé.',
            })

        } catch (err) {
            authStore.error = err.message
            bus.publish('auth:error', err.message)

        } finally {
            authStore.loading = false
            bus.publish('auth:loading', false)
        }
    })

    // ── auth:logout ──────────────────────────────────────────────────────────
    bus.subscribe('auth:logout', async () => {
        authStore.loading = true
        bus.publish('auth:loading', true)

        try {
            if (authStore.token) {
                await fetchLogout(authStore.token)
            }
        } catch (err) {
            // On vide le store même si la révocation échoue côté serveur
            console.warn('[auth] logout error:', err.message)
        } finally {
            authStore.clear()
            authStore.loading = false
            bus.publish('auth:loading', false)
            bus.publish('auth:changed')
            bus.publish('auth:guest')
        }
    })

    // ── auth:success ─────────────────────────────────────────────────────────
    bus.subscribe('auth:success', ({ user }) => {
        const groups = user?.groups ?? []
        console.log(`[auth] connecté : ${user?.username} [${groups.join(', ')}]`)
    })
}
