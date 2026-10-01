# codeIgniter

Gestion Authentification


# Users
arbph-dev/codeIgniter/
https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/docs/guides/api_tokens.md

//vendor/codeigniter4/shield/src/Entities/User.php

//vendor/codeigniter4/shield/src/Authentication/Traits/HasAccessTokens.php


vendor/codeigniter4/shield/src/Models/UserIdentityModel.php
generateAccessToken
https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/src/Models/UserIdentityModel.php#L146-L200
https://github.com/arbph-dev/codeIgniter/blob/ui_offest/vendor/codeigniter4/shield/src/Models/UserIdentityModel.php#L146-L200

revokeAccessToken
pas de trace

getAccessToken
https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/src/Models/UserIdentityModel.php#L191-L201
https://github.com/arbph-dev/codeIgniter/blob/ui_offest/vendor/codeigniter4/shield/src/Models/UserIdentityModel.php#L191-L201


login() API : OK
- auth('session')->check() pour vérifier les credentials.
- generateAccessToken() pour créer le token.
- Pas de attempt(), donc pas de login Session.

register() : c'est là qu'il y a le plus gros nettoyage
- le workflow natif Shield existe déjà ;
- notre code reproduit startLogin() + startUpAction() + Action::show();
- user_profils est encore complètement imbriqué dedans ;
- cette méthode mérite donc d'être reprise séparément.

logout() API : trop de travail manuel
- Shield sait déjà extraire et valider Bearer;
- Shield sait déjà retrouver le User ;
- Shield sait déjà gérer le token courant ;
et auth()->logout() tue en plus la session, ce qui mélange les deux mécanismes.

------

# ui
on retire les div panel-card 
- data-role="admin" data-index="-2"
- data-role="user" data-index="-1"
  
```
        <!-- ── AUTH BOARDS (hors menu / pagination) ── -->
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


