<?php

class controller extends template {

    public function beforeRun() {
        
        // De controller wordt bepaald
        $url = (!empty($this -> url[0]) ? $this -> url[0] : 'index');

        if (class_exists($url)) {
                $model = $url . '_model';

                $this -> controller = $this;

                if ($this -> render) {
                    $this -> template = new template($url);
                }

                if (is_object($this -> {$this -> currentModel})) {

                    if ($this -> render) {
                        $this -> template -> setModel($this -> {$this -> currentModel});
                    }
                }
          }
     }
    

    public function __construct() {

        // De standaard variablen worden ingesteld
        $this -> render = true;
        $this -> uses[] = 'Category';

         if (isset($this -> uses)) {
              foreach($this -> uses as $model) {
                   $modelName = ucfirst(strtolower(convert_table_name($model . '_model', 'join')));
                   $model = ucfirst(strtolower(convert_table_name($model, 'join')));

                   if (class_exists($modelName)) {
                         $this -> {$model} = new $modelName();
                         $this -> currentModel = $model;
                   } else {
                        $this -> template -> render = false;
                   }
              }
         }
         
              // Het model wordt aangemaakt
              $model = ucfirst(strtolower(convert_table_name((url(1) ? url(1) : 'index'), 'join')));
              $modelName = $model . '_model';

              if (class_exists($modelName)) {
                   $this -> {$model} = new $modelName();
                   $this -> currentModel = $model;
              } else {
                   $this ->  render = false;
                   echo 'Model <b>'. $modelName .'</b> niet gevonden';
                   exit;
              }
              


        // De eventuele POST headers worden opgehaald
        if ($_POST) {
             $this -> post = $_POST['post'];

             // Indien er bestanden aanwezig zijn worden deze ook in de POST array geplaatst;
             if ($_FILES && isset($_FILES['post'])) {
             	$files = array();
             	
             	foreach($_FILES['post'] as $type=>$array) {
             		foreach($array as $table=>$fields) {
             			foreach($fields as $fieldname=>$value) {
             				$files[$table][$fieldname][$type] = $value;
             			}
             		}
             	}
             	
             	// De files worden in de post array geplaatst
             	if (is_array($this -> post)) {
	             	foreach($this -> post as $table=>$array) {
	             		if (array_key_exists($table, $files)) {
	             			foreach($files[$table] as $field=>$value) {
	             				$this -> post[$table][$field] = $value;
	             			}
	             		}
	             	}
             	} else {
             		$this -> post = $files;
             	}
             }
        } else {
        	
        	// Er wordt bekeken of er enkel bestanden aanwezig zijn
        	if ($_FILES) {
        	$files = array();
             	
             	foreach($_FILES['post'] as $type=>$array) {
             		foreach($array as $table=>$fields) {
             			foreach($fields as $fieldname=>$value) {
             				$files[$table][$fieldname][$type] = $value;
             			}
             		}
             	}
             	
             	$this -> post = $files;
        	} else {        	
            	$this -> post = false;
        	}
        }
        


        $this -> url = explode('/', __URL__);

        foreach($this -> url as $url) {

            if (empty($url)) {
                $url = 'index';
            }

            // ##### De Action wordt bepaald #####
            if (isset($this -> controller) && method_exists($this -> controller, $url)) {
                $this -> action = $url;
            }

            // ##### Een eventueel type wordt bepaald #####
            if (isset($this -> action) && $this -> action != $url && !isset($this -> request)) {
                $this -> request = $url;
            }

            // ##### Een ID wordt bepaald #####
            if (ctype_digit($url)) {
                $this -> id = $url;
            } elseif(!isset($this -> id)) {
                $this -> id = 0;
            }
        }

        if (!isset($this -> action)) {
            $this -> action = 'index';
        }
    }

	public function loadCustoms() {

		// De javascript en css bestanden worden ingeladen
		$this -> template -> css('style');

	}


     public function loadPlugin($plugin) {

        $file = __DATA__ . 'library'. DS .'plugins'. DS . strtolower($plugin) . DS . 'class.'. strtolower($plugin) .'.php';

        // Er wordt bekeken of de plugin bestaat
        if (file_exists($file)) {
            require_once $file;

            $className = $plugin . 'Plugin';

            if (class_exists($className)) {
                $class = new $className($this);

                return $class;
            } else {
                die('De plugin ' . $plugin . ' kon niet gevonden worden!');
            }
        }

    }


    public function setFlash($type, $message, $fade='false') {
         if ($this -> template) {
              $this -> template -> noFlash = true;
              $_SESSION['flash'] = array($type, $message, $fade);
         }
    }

   

    public function __destruct() {

    }

}
