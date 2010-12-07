<?php

    // De standaard configuratie wordt ge�ncluded
    require_once __DATA__ . 'config/config.inc.php';
    require_once __DATA__ . 'library/functions.php';

    if (isset($_GET['url']) && !empty($_GET['url'])) {
        if (preg_match("/[A-Z]/", $_GET['url'])) {
            navigeer(strtolower($_GET['url']));
        }

        $link = explode("/", $_GET['url']);
    } else {
        $link = array('index');
    }

    // De http constante wordt ingesteld
    define('HTTP', 200);

    function callClass() {

         $classCall = (url(1) ? url(1) : 'index');

        if (class_exists($classCall)) {
            $class = new $classCall();

            if (method_exists($class, 'beforeRun')) {
                $class->beforeRun();
            }

            if (url(2)) {

                if (method_exists($class, url(2))) {
                    $class->{url(2)}();
                 } else {
                    $class->__index();
                 }

            } else {
                $class->__index();
            }
        } else {
            require_once '404.php';
            exit;
        }
    }

    function __autoload($class) {
        if ($class == 'lib') {
            require_once __DATA__ . 'library/class.lib.php';
        }

        if (preg_match('/_model/', $class)) {
             $className = strtolower(str_replace('_model', '', $class));

             if (file_exists(__DATA__ . 'application/models/class.' . $className . '.php')) {
                 require_once __DATA__ . 'application/models/class.' . $className . '.php';
             }
        }

        if (file_exists(__DATA__ . 'library/class.' . $class . '.php')) {
            require_once __DATA__ . 'library/class.' . $class . '.php';
        } elseif (file_exists(__DATA__ . 'application/controllers/class.' . $class . '.php')) {
            require_once __DATA__ . 'application/controllers/class.' . $class . '.php';
        }
    }

    callClass();

?>
