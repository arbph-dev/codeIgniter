**Fichiers**
- REPO : (/ui.html)[/public/ui.html]
- RESSOURCES :
 -  https://github.com/arbph-dev/codeIgniter-appCms/blob/main/assets/ui_html.md

/uiapp.js#L185
 
 
Ce code JavaScript initialise dynamiquement un menu de navigation déroulant ou accordéon à partir d'un tableau de données nommé _pages.
Voici les fonctionnalités principales du code :
Génération de la structure HTML : Pour chaque page contenue dans _pages, le code crée un panneau de navigation (menu_panel) comprenant un titre (panel.title), un bouton avec une icône, et un sous-menu (sub_menu).
Création des sous-sections : Pour chaque section d'une page, le code génère un élément de liste (<li>) et l'ajoute au sous-menu.
Gestion de la navigation (clic sur une section) : Lorsqu'un utilisateur clique sur un élément du sous-menu :
L'action par défaut et la propagation de l'événement sont stoppées (preventDefault, stopPropagation).
Les fonctions switchPanel(index) et switchSection(sindex) sont exécutées pour afficher le contenu correspondant.
Le menu se ferme automatiquement (en retirant la classe 'open' sur grand écran ou en appelant closeSidebar() sur mobile).
Interactions avec le panneau principal :
Au clic : Ouvre ou bascule le panneau via openMenuPanel(index).
Survol / Sortie de la souris (mouseleave) : Sur écran d'ordinateur (largeur > 768px), le panneau se ferme automatiquement dès que la souris quitte sa zone.
