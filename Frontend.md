**Fichiers**
- REPO : [/ui.html](/public/ui.html)
- RESSOURCES :
 -  https://github.com/arbph-dev/codeIgniter-appCms/blob/main/assets/ui_html.md

Premiere priorité stabiliser l' ihm 

Stop à la construction dynamique à réserver au workbench ou composant 




## Gestion des panneaux 
Les panels sont décrits ici : 
- https://github.com/arbph-dev/codeIgniter-appCms/blob/main/assets/ui_html.md#structure
- https://github.com/arbph-dev/codeIgniter-appCms/blob/main/assets/ui_html.md#css-associe

On a introduit deux types de panels 
### div.panel-card + data-role 
- [admin](/public/ui.html#L69 )
- [user](/public/ui.html#L82)

```html
 <div class="panel-card hidden" data-role="admin" data-index="-2">
```

### div.panel-card
```html
 <div class="panel-card" data-index="0">
```
- [user](/public/ui.html#L82)






La liste des Panels du menu est une liste sans panneau admin,user que l' on a dissocier mais il y a un bug à l' affichage 
 
La fonction qui gère les panneaux doit cacher tout les Panels et se séparer de l affichage 
 
La liste des Panels du menu est une liste sans panneau admin,user
La liste des panels a masqué c est toute la liste 
Pour l affichage une div panel-card est dans la liste des pages, on utilise un offset 2 car deux panels admin user selon auth user



 [/assets/js/uiapp.js#L185](public/assets/js/uiapp.js#L185)
 


Survol / Sortie de la souris (mouseleave) : 
 Sur écran d'ordinateur (largeur > 768px), le panneau se ferme automatiquement dès que la souris quitte sa zone.
