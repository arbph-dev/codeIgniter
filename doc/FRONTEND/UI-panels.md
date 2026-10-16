# Panels - Onglets

- main
- nav
- header
- footer

## Structure

```
main
	article.panel-card
		h2.panel-title
		p.panel-description 
		div.section-tab
			div.tab-headers
			section.tab-content
				h3
```

```
main
main > article.panel-card
main > article.panel-card > h2.panel-title
main > article.panel-card > p.panel-description 
main > article.panel-card > div.section-tab
main > article.panel-card > div.section-tab > div.tab-headers
main > article.panel-card > div.section-tab > section#id.tab-content
main > article.panel-card > div.section-tab > section#id.tab-content > h3
```
### css associe
- panel-card
- panel-title
- panel-description
- section-tab
- tab-headers
- tab-btn et tab-btn active
- tab-content et tab-content active



### Panel générique

```html
<article id="art0" class="panel-card">
	<h2 class="panel-title">Actualités</h2>
	<p class="panel-description">Les dernières informations</p>
	<div class="section-tab">
		<div class="tab-headers"></div>
		<section id="art0-s0" class="tab-content active">
			<h3>Dernièrement</h3>
			<div id="art0-s0-body">....</div>
		</section>
		<section id="art0-s1" class="tab-content">
			<h3>Déploiement</h3>
			<div id="art0-s1-body">....</div>
		</section>
	</div>
</article>
```

### Panel admin

Reprend la structure d'un Panel générique mais réserve des éléments
- art-admin-home-body
- art-admin-user-list


```
main > article.panel-card > div.section-tab > section.tab-content > div#art-admin-home-body
main > article.panel-card > div.section-tab > section.tab-content > div#art-admin-user-list
```

```html
<article id="art-admin" class="panel-card hidden">
	<h2 class="panel-title">Administration</h2>
	<p class="panel-description">Tableau de bord administrateur</p>
	<div class="section-tab">
		<div class="tab-headers"></div>
		<section id="art-admin-home" class="tab-content active">
			<h3>Home</h3>
			<div id="art-admin-home-body"><!-- contenu ultérieur --></div>
		</section>
		<section id="art-admin-user" class="tab-content">
			<h3>User</h3>
			<div id="art-admin-user-list"><!-- contenu ultérieur --></div>
		</section>
	</div>
</article>
```

### Panel user
réserve des éléments
- art-user-home-body

```html
<article id="art-user" class="panel-card hidden">
	<h2 class="panel-title">Mon espace</h2>
	<p class="panel-description">Profil et inscription</p>
	<div class="section-tab">
		<div class="tab-headers"></div>
		<section id="art-user-home" class="tab-content active">
			<h3>Board User</h3>
			<div id="art-user-home-body"><!-- contenu ultérieur --></div>
		</section>
	</div>
</article>
```


## Notes a reprendre
- https://github.com/arbph-dev/codeIgniter/blob/master/Frontend.md
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/FRONTEND/JS-uiapp.md
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/FRONTEND/UI-footer.md
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/FRONTEND/UI-header.md
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/FRONTEND/UI-nav.md
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/FRONTEND/UI-panels.md
- https://github.com/arbph-dev/codeIgniter/blob/master/doc/FRONTEND/UI.md

<!-- 
# Points important

- Usage des attributs aria
- Usage des id sur élément structurel
- style css à gérér

#### Usage des attributs aria
est il justifié ? ou est il employé ?

- data-role="user"
- data-index="-1"

expliquer lecture ecriture creation pour le référentiels


#### Usage des id sur élément structurel
est il justifié ? ou est il employé ?

- main
- header
- footer

#### style css à gérér

Faire un point entre : uistyle.css et [PanelStyles.js](https://github.com/arbph-dev/codeIgniter/blob/master/public/assets/js/ui/workbench/core/PanelStyles.js)

Jetons CSS structurels des Panels Workbench.
- Noms de classes uniquement (pas de couleurs).
- Theming clair/sombre → variables CSS (--wb-*), pas ce module.
- Override partiel via createPanelStyles(defaults, override).

- main
- header
- footer

#### identifier les éléments dom - Règle DOM000
Etablir une convention pour identifier les éléments **DOM** pour les retrouver et les exploiter

```
header#header > div.header-actions > button#themeBtn
```



## structure






```html
<div class="panel-card">
    <h2 class="panel-title">...</h2>

    <p class="panel-description">.....</p>

    <div class="section-tab">
        <div class="tab-headers">
          <button class="tab-btn active">...</button>
          <button class="tab-btn" data-tab="info-0">Informations</button>
        </div>


        <div id="..." class="tab-content active">
        </div>

        <div id="..." class="tab-content">
        </div>
    </div>

</div>   
```



```
<div id="admin-board-body">
    <!-- contenu ultérieur -->
</div>
```

```
<div class="panel-card hidden" data-role="admin" data-index="-2">
    <h2 class="panel-title">Administration</h2>
    <p class="panel-description">Tableau de bord administrateur</p>
    <div class="section-tab">
        <div class="tab-headers"></div>
        <div id="admin-home" class="tab-content active">
            <h3>Admin</h3>
            <div id="admin-board-body"><!-- contenu ultérieur --></div>
        </div>
    </div>
</div>
```

### Panel user



```
<div class="panel-card hidden" data-role="user" data-index="-1">
    <h2 class="panel-title">Mon espace</h2>
    <p class="panel-description">Profil et inscription</p>
    <div class="section-tab">
        <div class="tab-headers"></div>
        <div id="user-home" class="tab-content active">
            <h3>Espace utilisateur</h3>
            <div id="user-board-body"><!-- formulaire register OU dashboard user --></div>
        </div>
    </div>
</div>
```
-->



