<?php

	// Voor flash en andere externe requests wordt indien noodzakelijk de sessie al ingesteld
	if (isset($_POST['session_id'])) {
		$session_id = $_POST['session_id'];
		
		session_id($session_id);
	}

    session_start();
    
    // De url array wordt gedefinïeerd
	$url = (empty($_GET['url']) ? '' : $_GET['url']);
    define('__URL__', $url);

    define('__DATA__', dirname(dirname(__FILE__)) . '/');
    require_once __DATA__ . 'library/bootstrap.php';
?>
