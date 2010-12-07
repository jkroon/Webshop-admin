<?php

     // De domeinnaam wordt vastgelegd
     $domain = 'admin';
     $submap = '';  // Empty
     $www = false;
	 
	 // Aantal seconde dat de sessie voor de administrator blijft bestaan
	 define('ADMIN_MAX_LIFE_TIME', 3600);


     if ($www) {
         $www = 'http://www.';
     } else {
         $www = 'http://';
     }
	
	if (!empty($submap)) {
	    $submap = $submap . '/';
	}

     // ########## Website domain ##########
     define('__DOMAIN__', $www.$domain . '/' . $submap);
	 

     // ########## Directory separator ##########
     define('DS', '/');

     // ########## Javascript root ##########
     define('__JSROOT__', '/');


     // ########## Beveiliging van het script ##########
     define('__SALT__', 'asdfs43sDf42!!%&#@dvEsa!@QQ');
     define('__PEPER__', 'dt767Tf%&2S*(DsXzaww!@@#e#');


     // ########## De database connectie wordt ge�nstant�eerd ##########
     $host = 'localhost';
     $user = 'jeffrey';
     $pass = 'test12345';
     $db = 'webshop';

     $link = mysql_connect($host, $user, $pass);
     mysql_select_db($db, $link);
     
     
     // ######################################################## //
     // ########## Vanaf hier niks meer aanpassen!!!! ########## //
     // ######################################################## //
     
     define('LINK', $link);
     define('__DATABASE__', $db);

?>
