# codeIgniter

## UI
- [2026-10-01 : Remise a plat uiapp.js](/doc/notes/2026-10-01.md#cUI)

## Gestion Authentification
- backend
  - [2026-09-30](/doc/notes/2026-09-30.md)
  - [2026-10-01 : codeigniter / shield / users](/doc/notes/2026-10-01.md#codeigniter--shield--users)
- frontend
  - [2026-10-02](/doc/notes/2026-10-02.md)
  - [2026-10-03](doc/notes/2026-10-03.md) : register
    - [2026-10-03-001](/doc/notes/2026-10-03-001.md) : Détail de l'interaction : ToolbarAuthPanel ↔ Bus ↔ uiapp.js
    - https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-03-001.md#-r%C3%A9sum%C3%A9-des-responsabilit%C3%A9s
    - https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-03-001.md#2-ce-que-le-bus-doit-exposer
    - https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-03-001.md#3-%C3%A9dition-de-authcontrollerjs
    - https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-03-001.md#4-impl%C3%A9mentation-de-fetchactivate-dans-authservicejs
    - https://github.com/arbph-dev/codeIgniter/blob/master/doc/notes/2026-10-03-001.md#5-la-vraie-modification-dans-authpanelbase
    - [2026-10-03-001-05](/doc/notes/2026-10-03-001-05.md)
    - [2026-10-04](/doc/notes/2026-10-04.md) : amélioration  AuthPanelBase / ToolbarAuthPanel
    - [2026-10-04-001](/doc/notes/2026-10-04-001.md) inscription en attente
## Taches

- [ ] Headers / js : Faire un choix affectation event ui dans html ou dans le code js
- [ ] Revoir nécessité des id sur les éléments de structure main, header, nav ; but simplifier les selectors et le code css
- [ ] Panels - Onglets / Structure : panel-card a faire évoluer en article et div.section-tab en sections



## Envrionnement
```
composer show codeigniter4/shield
```
name     : codeigniter4/shield
descrip. : Authentication and Authorization for CodeIgniter 4
keywords : Authentication, authorization, codeigniter, codeigniter4
versions : * v1.3.0
released : 2026-03-16, 6 months ago
type     : library
