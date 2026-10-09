files : [`/ui-components/apex.html`](/public/ui-components/apex.html)
url : 


a ce stade 
- on conserve uiapp.js pour les applications
- on conserve uiapp2.js pour les composants
- les documents relatifs aux composants sont placés dans le dossier /public/ui-components/
 
on documentera les composants dans la page dedie au composant lui meme

# [/ui-components/apex.html](/public/ui-components/apex.html)

on remonte le composant apex dans un `main#stack > div.panel-card` / type de graphique
- editer main#stack > div.panel-card > h2.panel-title
- editer main#stack > div.panel-card > p.panel-description 

```html

```
    <div class="panel-card" data-index="0">
        <h2 class="panel-title">Hypersynchronisme</h2>
    
        <p class="panel-description">
            Le graphique doit présenté l'hypersynchronisme
        </p>


    </div>    
le conteneur des sections 
- main#stack > div.panel-card > div.section-tab
le header des sections 
- main#stack > div.panel-card > div.section-tab > div.tab-headers


Première section, on met le composant 
Seconde section : description
Troisième section : tâches

on met le composant en premiere section en evidence avec active

main#stack > div.panel-card > div.section-tab > div#id.tab-content
main#stack > div.panel-card > div.section-tab > div#id.tab-content > h3
        


        <div class="section-tab">
            <div class="tab-headers"></div>
            
            <div id="apex-0" class="tab-content active">
                <h3>Graphique</h3>
                  <p>Le couple selon la vistesse</p>
                
                  <h4>line</h4>
                  <div id="APEX_LIGNE_1" class="cp_apex" data-chart="line"></div>

            </div>

            <div id="apex-description" class="tab-content">
                <h3>Description</h3>
                  <p>Le composant est importé dans le script de niveau application uiapp.js</p>
                
                  <h4>Mise en oeuvre</h4>
                  on reserve un container div
                  on attribue un id au container
                  on attribue une classe cp_apex
                  on attribue un type de graphique via data-chart="line"
                  <code>
                    &lt;div id="APEX_LIGNE_1" class="cp_apex" data-chart="line"&gt;&lt;/div&gt;
                  </code>
            </div>

            <div id="apex-todo" class="tab-content">
                <h3>Todo</h3>
                  <p>Décrire les graphiques et leurs utilisation</p>
                  <p>Décrire la création des graphiques</p>
            </div>

        </div>


<!--   -->
