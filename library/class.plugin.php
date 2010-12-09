<?php

class plugin {

     public $post;
     public $model;

     public function __construct() {

          if ($_POST) {
               $this -> post = $_POST;
          }

          if (method_exists(get_class($this), 'beforeRun')) {
               $this -> beforeRun();
          }

     }

     public function loadExternalFile($type, $plugin, $file) {

     if (!isset($_SESSION['template'])) {
          $_SESSION['template'] = array();
     }

     $session = $_SESSION['template'];

	switch($type) {
	    case "script":
              if (!isset($session['plugin']['script']) || !in_array($file, $session['plugin']['script'])) {
                    $_SESSION['template']['plugin']['script'][] = 'library/plugins/' . $plugin . '/' . $file;
              }
	    break;

	    case "css":
		//$this -> template -> css('plugins' . $file);
	    break;
	}

    }

    public function setModel($model) {
         $this -> model = $model;
    }


    public function query($query, $die=true) {
        $result = mysql_query($query);

        if ($die && !$result) {
            die(mysql_error());
        }

        return $result;
    }

}