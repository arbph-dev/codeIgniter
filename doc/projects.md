### COMPOSANTS
Je souhaiterais améliorer le composant

documentation/COMPOSANTS/INDEX.md


Je souhaiterais intégrer le composant   dans

app/Views/cms/components/debug_overlay.php
https://github.com/arbph-dev/codeIgniter/blob/master/app/Views/cms/components/debug_overlay.php

----
### ui
je souhaite exploiter les autocomplete et dialogpickers dans des dialog

public/assets/js/ui/shared/RelationPickerDialog.js
https://github.com/arbph-dev/codeIgniter/blob/master/
https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/ui/shared/RelationPickerDialog.js


assets/js/ui/shared/DialogManager.js
https://github.com/arbph-dev/codeIgniter/blob/master/public/
https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/ui/shared/DialogManager.js

assets/js/features/typevoie/typevoie.service.js
https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/typevoie/typevoie.service.js
assets/js/ui/workbench/organisation/OrganisationWorkbench.js
https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/ui/workbench/organisation/OrganisationWorkbench.js
assets/js/features/mot/mot.service.js
https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/mot/mot.service.js
assets/js/features/codepostal/codepostal.service.js
https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/features/codepostal/codepostal.service.js

### DialogManager (a étoffer)

/assets/js/ui/shared/DialogManager.js

### gmao
- https://github.com/arbph-dev/codeIgniter-appCms/blob/main/project/daily/2026-09-26-004.md

- https://github.com/arbph-dev/codeIgniter-appCms/tree/main/documentation/METIERS/MAINTENANCE
### tasks

- [ ]  Valider ajout des tables : projects , project_members (necessaire pour ajuster les droits)
- [ ]  relation projets - Organisation/entreprise/Etablissement , user - personne
    - peu d'intérêt sans telephone
- [ ]  user_profils avec tel fixe, mobile, index user_id user relation ou id personne et organisation
- [ ]  voir possibilité de trouver: user - Etablissement avec user - personne et personne - Organisation/entreprise/Etablissement
- [ ] gestion des relations voir https://github.com/arbph-dev/codeIgniter-appCms/blob/main/project/Tasks.md#relation
- [ ] projets d'entreprise, établissement - lien maintenance système / site ,users/ personnel
- [ ] export vers obsidian (formatage ave icone )
  
- créer un projet avec des taches
- visualisation, 
	- liste projets,
	- liste taches par projet
	- liste de toutes les taches / responsables, personne affectée,
	- gantt / mermaid
- Edition modification détail

https://github.com/arbph-dev/codeIgniter-appCms/blob/main/project/daily/2026-09-26-003.md

### RelationPickerDialog

```js
// assets/js/ui/shared/RelationPickerDialog.js
// ─────────────────────────────────────────────────────────────────────────────
// Dialog générique de sélection d'une entité liée.
//
// Responsabilités :
//   • créer le <dialog> DOM et l'enregistrer dans DialogManager
//   • gérer la recherche avec debounce via fetchFn
//   • afficher les résultats dans une table
//   • publier dialogManager.select(id, item) sur sélection de ligne
//
// Ce que RelationPickerDialog ne fait PAS :
//   • ne connaît pas le champ Form qui l'a ouvert
//   • ne transforme pas l'item (labelKey, valueKey) — c'est Form.js qui sait
//     quoi extraire de l'item brut retourné dans dialog:select
//   • ne stocke aucun état persistant entre deux ouvertures
//
// Paramètres :
//   id         {string}    — ID unique du dialog (= sourceId dans dialog:select)
//   title      {string}    — titre affiché dans le header
//   fetchFn    {Function}  — async (q: string) => object[]
//   columns    {Array}     — [{key, label}] pour la table de résultats
//   minLength  {number}    — nb de chars avant déclenchement (défaut : 2)
```


```js
import { create, clear, table, notice } from '/assets/js/core/domhelper.js'
import { dialogManager }                from '/assets/js/ui/shared/DialogManager.js'

export class RelationPickerDialog
```



source : public/assets/js/ui/workbench/personne/PersonneWorkbench.js
```js
    _createDialogs()
    {
        this._personnePicker = new RelationPickerDialog({
            id        : 'dialog_personne_picker',
            title     : 'Sélectionner une personne',
            fetchFn   : (q) => fetchPersonneLike({ q, len: 20 }),
            columns   : [
                { key: 'nom_complet',    label: 'Nom'      },
                { key: 'date_naissance', label: 'Né(e) le' },
            ],
            minLength : 2,
        }).render()

        this._orgPicker = new RelationPickerDialog({
            id        : 'dialog_org_picker',
            title     : 'Sélectionner une organisation',
            fetchFn   : (q) => fetchOrgLike({ q, len: 20 }),
            columns   : [
                { key: 'nom',   label: 'Nom'   },
                { key: 'siren', label: 'SIREN' },
            ],
            minLength : 2,
        }).render()

        // dialog_merge_picker : même fetchFn que dialog_personne_picker
        // mais sourceId distinct → le mergeHandler dans PersonneDetailPanel
        // discrimine sur sourceId === 'dialog_merge_picker'
        this._mergePicker = new RelationPickerDialog({
            id        : 'dialog_merge_picker',
            title     : 'Fusionner dans…',
            fetchFn   : (q) => fetchPersonneLike({ q, len: 20 }),
            columns   : [
                { key: 'nom_complet',    label: 'Nom'      },
                { key: 'date_naissance', label: 'Né(e) le' },
            ],
            minLength : 2,
        }).render()
    }
```


```
// assets/js/features/typevoie/typevoie.service.js
// ─────────────────────────────────────────────────────────────────────────────
// CRUD complet (contrairement à CodePostal qui est read-only).
//
// Adapté depuis old/typevoie.service.js :
//   - apiFetch depuis le chemin architecture new
//   - fetchTvLike retourne items[] directement (compatible RelationPickerDialog)
//   - fetchTv : param id retiré -> fetchTvById couvre ce cas
//   - saveTv  : pattern POST/PUT aligné sur image.service.js


// assets/js/features/personne/personne.service.js
/**
 * Recherche rapide (suggest) — retourne un tableau plat de personnes.
 * Utilisé par RelationPickerDialog (dialog_personne_picker, dialog_merge_picker).
 */
export async function fetchPersonneLike({ q, len = 20 } = {})

// assets/js/ui/workbench/organisation/OrganisationWorkbench.js
// ─────────────────────────────────────────────────────────────────────────────
// 2 zones : list (left) + detail (center, TabSystem intégré dans OrgDetailPanel)
//
// Dialogs :
//   dialog_adresse — RelationPickerDialog pour adresse_id
//                    (fetchAdresseLike → suggest AdresseModel)
//
// onSave(id, data) — id=null création, id>0 mise à jour partielle
//   Le Workbench fait toujours saveOrg({ id, ...data }) — le backend
//   n'applique que les allowedFields présents dans data.
//
// Pagination via onPage(fn) — cohérent avec le contrat callback panels.

// assets/js/features/mot/mot.service.js
// ─────────────────────────────────────────────────────────────────────────────
// Service Mot — new architecture.
// PK : mot_id (pas id) — tous les appels utilisent mot_id.
//
// fetchMot      — liste paginée (q, page, perPage)
// fetchMotLike  — autocomplete → items[] plat
// fetchMotBatch — multi-IDs en un appel → items[] plat (lazy load ImageTagger)
// saveMot       — POST (id=null) / PUT (id>0)
// deleteMot     — DELETE
// ─────────────────────────────────────────────────────────────────────────────
/**
 * Autocomplete — retourne un tableau plat.
 * Compatible RelationPickerDialog.fetchFn et champ autocomplete.
 * @param {object} params
 * @param {string} params.q
 * @param {number} [params.len]
 * @returns {Promise<object[]>}   [{ mot_id, mot_lbl }, …]
 */
export async function fetchMotLike({ q = '', len = 10 } = {})


// assets/js/features/codepostal/codepostal.service.js
// ─────────────────────────────────────────────────────────────────────────────
// Référentiel read-only — pas de save ni delete.
//
// Adapté depuis old/codepostal.service.js :
//   - apiFetch depuis le chemin architecture new
//   - fetchCpLike retourne items[] directement (pas { data:[] })
//     => compatible RelationPickerDialog.fetchFn
//   - fetchCp : paramètres q / codepostal / codeinsee conservés
```

