# transposition de la partie admin (vue - spa)

préparer la transposition de la partie admin : https://zealot.fr/admin

basé sur 
https://github.com/arbph-dev/codeIgniter/blob/master/app/Controllers/Admin.php
https://github.com/arbph-dev/codeIgniter/blob/master/app/Views/cms/admin.php
https://github.com/arbph-dev/codeIgniter/blob/master/app/Views/layouts/cms.php
https://github.com/arbph-dev/codeIgniter/blob/master/app/Views/cms/components/debug_overlay.php

dans app/Views/cms/admin.php les données sont transmises par app/Controllers/Admin.php 
```
const USERS = <?= json_encode($users ?? [], JSON_UNESCAPED_UNICODE) ?>;
```

dans un premier temps j'ai déjà besoin d'une liste des utilisateurs
	pour une api task a venir
	pour l'administration
	pour les api, exemple messagerie users			

ensuite une liste des utilisateurs avec filtres sur rôles et permissions

dans un second temps il faut exploiter les logs de connexion
 
on va distinguer les travaux front et back end
On aura besoin des ressources
```

use CodeIgniter\Shield\Authentication\Authenticators\Session;  
use CodeIgniter\Shield\Authentication\HMAC\HmacEncrypter;  
use CodeIgniter\Shield\Authentication\Passwords;  
use CodeIgniter\Shield\Entities\AccessToken;  
use CodeIgniter\Shield\Entities\User;  
use CodeIgniter\Shield\Entities\UserIdentity;  
use CodeIgniter\Shield\Exceptions\LogicException;  
use CodeIgniter\Shield\Exceptions\ValidationException;  

---------------------------------------------------------------------
CodeIgniter\Shield\Entities\AccessToken
/vendor/codeigniter4/shield/src/Authentication/Authenticators/AccessTokens.php
https://github.com/arbph-dev/codeIgniter#codeignitershieldentitiesaccesstoken

use CodeIgniter\Shield\Entities\User;
https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/src/Entities/User.php
  
use CodeIgniter\Shield\Entities\UserIdentity;  
https://github.com/arbph-dev/codeIgniter/blob/master/vendor/codeigniter4/shield/src/Entities/UserIdentity.php
```
