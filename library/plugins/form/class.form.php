<?php

class formPlugin extends plugin {

    protected $data;
    protected $validation;

    protected $fields;
    protected $currentTable = 'Form';

    protected $formAttributes;
    protected $attributes;
    protected $html;

    protected $order = false;
    protected $outputSent;

    protected $ignore;
    protected $useOnly;


    // De constructor wordt aangemaakt
    public function __construct() {
    	parent::__construct();
    	parent::loadExternalFile('script', 'form', 'script');
    }


    // De functie om een model te binden aan het object
    public function bindModel($model) {
         $this -> model = $model;
    }


    // Het aanmaken van het formulier
    public function create($table, $attributes=array()) {

	if (!isset($attributes['action'])) {
	    $attributes['action'] = '';
	}

	if (!isset($attributes['method'])) {
	    $attributes['method'] = 'post';
	}

	$this -> currentTable = ucfirst(strtolower(convert_table_name($table, 'join')));
        $this -> formAttributes['start'] = $attributes;
    }


    // Het aanmaken van een input
    private function input($name, $attributes='', $table='') {
        $table = (empty($table) ? $this -> currentTable : ucfirst(strtolower(convert_table_name($table, 'join'))));
        $name = strtolower($name);

        $this -> data[$table]['input'][] = $name;
        $this -> attributes[$table][$name] = $attributes;
        $this -> fields[$table][] = $name;
    }


    // Het aanmaken van een tekstveld
    public function text($name, $attributes='', $table='') {

        // Het type attribuut wordt aangemaakt
        $attributes['type'] = 'text';

        // Er wordt een input van het veld gemaakt
        self::input($name, $attributes, $table);
    }

    
    // Het aanmaken van een textarea
    public function textarea($name, $attributes='', $table='') {
        $table = (empty($table) ? $this -> currentTable : ucfirst(strtolower(convert_table_name($table, 'join'))));
        $name = strtolower($name);

        $this -> data[$table]['textarea'][] = $name;
        $this -> attributes[$table][$name] = $attributes;
        $this -> fields[$table][] = $name;    	
    }
    

    // Het aanmaken van een hidden field
    public function hidden($name, $attributes='', $table='') {

        // Het type attribuut wordt aangemaakt
        $attributes['type'] = 'hidden';

        // Er wordt een input veld aangemaakt
        self::input($name, $attributes, $table);

    }


    // Het aanmaken van een checkbox
    public function checkbox($name, $attributes='', $table='') {

         // Het type attribuut wordt aangemaakt
         $attributes['type'] = 'checkbox';

         // Er wordt bekeken of de checkbox standaard op true moet staan
         if (isset($attributes['defaultChecked']) && !$this -> post) {
              $attributes['checked'] = 'checked';
              unset($attributes['defaultChecked']);
         }

         // Er wordt een veld aangemaakt
         self::input($name, $attributes, $table);

    }


    // Het aanmaken van een selectieformulier
    public function select($name, $attributes='', $table='') {
        $table = (empty($table) ? $this -> currentTable : ucfirst(strtolower(convert_table_name($table, 'join'))));
        $name = strtolower($name);

        // De velden worden aan de array toegevoegd
        $this -> data[$table]['select'][] = $name;
        $this -> attributes[$table][$name] = $attributes;
        $this -> fields[$table][] = $name;

    }


    // De automatische genereer functie voor een tabel
    public function auto_generate($table) {

        // De tabelnaam wordt naar enkelvoud geparsed
        $table = safe(convert_table_name($table, 'outer_join'));

	// De current tabel wordt ingesteld
	$this -> currentTable = ucfirst(strtolower(convert_table_name($table, 'join')));

        // De query wordt uitgevoerd
        $result = parent::query("DESCRIBE `". $table ."`");

        // Alle velden worden door de check heen gehaald
        while($var = mysql_fetch_assoc($result)) {

            // De waarden worden in een variable geplaatst
            $field_name = $var['Field'];
            $data_type = $var['Type'];
            
            // Het data_type wordt bepaald
            $length = preg_match("/\((.*)\)/", $data_type, $matches);
            if ($length) {
                $data_type = preg_replace("/\((.*)\)/", "", $data_type);
                $length = $matches[1];
            }
            
            
            // De array voor de atributen wordt leeg gemaakt
            $attributes = array();

            
            // Alle velden zoals smallint, mediumint, bigint en int worden 1 gemaakt
            if (preg_match("/^(tinyint|smallint|mediumint|bigint|int)$/", $data_type)) {
                $data_type = 'int';
            }


            // Er wordt gekeken of er een associatie aanwezig is
            if (preg_match("/_id$/", $field_name)) {

                $explode = explode('_', $field_name);
                $table_name = convert_table_name($explode[0], 'outer_join');

                // Er wordt gecontroleerd of de tabel bestaat
                $query = "SELECT * FROM `". safe($table_name) ."`";
                $result1 = plugin::query($query, false);

                if ($result1) {

                     // Er wordt bekeken welke velden moeten worden opgehaald
                     $query = "DESCRIBE `". safe($table_name) ."`";
                     $result1 = plugin::query($query, false);

                     while($var1 = mysql_fetch_assoc($result1)) {
                          if (preg_match('/varchar/', $var1['Type'])) {
                               $name_field = $var1['Field'];
                               break;
                          }
                     }

                    $data_type = 'select';
                    $attributes['auto_select'] = array($table_name, 'id', $name_field);
                }
            }


	    // Primaire sleutels worden genegeerd
	    if ($var['Key'] == 'PRI') {
		$data_type = '';
	    }


            // Aan de hand van het datatype wordt het uiteindelijke input veld bepaald
            switch($data_type) {
                case "varchar":
                    $attributes['maxlength'] = $length;
                    self::text($field_name, $attributes, $table);
                break;

                case "int":
                    $attributes['maxlength'] = $length;
                    $attributes['validate'] = 'isDigit';
                    self::text($field_name, $attributes, $table);
                break;

                case "decimal":
                    $attributes['validate'] = $var['Type'];
                    self::text($field_name, $attributes, $table);
                break;

                case "select":
                    self::select($field_name, $attributes, $table);
                break;
            }

        }
    }



    public function end($attributes) {

	if (!is_array($attributes)) {
	    $value = $attributes;

	    $attributes = array();
	    $attributes['type'] = 'submit';
	    $attributes['value'] = $value;
	}

	// De verschillende attributen worden geladen
	if (!isset($attributes['type'])) {
	    $attributes['type'] = 'submit';
	    $attributes['value'] = 'Verzenden';
	}

	$this -> formAttributes['end'] = $attributes;
    }


    public function createOrder($array) {

	foreach($array as $table=>$fields) {
	    $table = ucfirst(strtolower(convert_table_name($table, 'join')));

	    $this -> order[$table] = $fields;
	}

    }


    public function prepare() {

        // Het formulier wordt gegenereerd
	if (is_array($this -> data)) {
	    foreach($this -> data as $table=>$table_content) {


		foreach($table_content as $type=>$array) {

		    // Er wordt bekeken of er enkel opgegeven velden mogen worden gebruikt
		    if (isset($this -> useOnly[$table]) && count($this -> useOnly[$table]) > 0) {
			$use_fields = $this -> useOnly[$table];
			$all_fields = $this -> fields[$table];

			foreach($all_fields as $all_field) {
			    if (!in_array($all_field, $use_fields)) {
				$this -> ignore[$table][] = $all_field;
			    }
			}
		    }


              // Er wordt gekeken of er een POST aanvraag is, zo ja dan worden de values geladen
              if ($this -> model && $this -> model -> data) {
                   if (array_key_exists($table, $this -> model -> data)) {
                        foreach($this -> model -> data[$table] as $field=>$value) {
                             $this -> attributes[$table][$field]['value'] = $value;
                        }
                   }
              }


              // Er wordt bekeken of er fouten aanwezig zijn
              if ($this -> model && $this -> model -> validateErrors) {
                   $errors = $this -> model -> validateErrors;

                   if (array_key_exists($table, $errors)) {
                        foreach($errors[$table] as $field=>$error) {
                             $this -> attributes[$table][$field]['error'] = $error;
                        }
                   }

              }


		    // Er wordt bekeken of er velden genegeerd moeten worden
		    if (isset($this -> ignore[$table])) {
			$ignore_fields = $this -> ignore[$table];

			foreach($ignore_fields as $ignore_field) {
			    $array_key = array_search($ignore_field, $array);
			    
			    if ($array_key !== false) {
				unset($array[$array_key]);
			    }
			}
		    }


		     // De INPUT velden worden ingeladen
		     if ($type == 'input') {
			  foreach($array as $field) {
			      
			       // De attributen worden in een string geplaatst
			       $attributes = $this -> attributes[$table][$field];

                      // Een eventuele foutmelding wordt gecreëerd
                      if (array_key_exists('error', $attributes)) {
                           $errorClass = ' formError';
                           $errorMsg = $attributes['error'];
                      } else {
                           $errorClass = '';
                           $errorMsg = false;
                      }

			       // De variable html wordt opnieuw aangemaakt
			       $html = '<div id="div'. ucfirst(strtolower($table)) . ucfirst(strtolower($field)) .'" class="formDivPlaceholder'. $errorClass .'"><div class="formDivPlaceholderIn">';

			       // Een eventueel label wordt aangemaakt
			       if (array_key_exists('label', $attributes)) {
				    $html .= '<label id="label'. ucfirst(strtolower($table)) . ucfirst(strtolower($field)) .'">' . $attributes['label'] . '</label>';
				    unset($attributes['label']);
			       }


                      // Indien er sprake is van een checkbox, wordt deze gechecked indien nodig
                      if ($attributes['type'] == 'checkbox' && $this -> model -> data[$table]) {
                           if (!empty($this -> model -> data[$table][$field])) {
                                $attributes['checked'] = 'checked';
                           } else {
                                if (isset($attributes['checked'])) {
                                   unset($attributes['checked']);
                                }
                           }
                      }


			       // Het inputveld wordt aangemaakt
			       $html .= '<input name="post['. ucfirst(strtolower($table)) .']['. strtolower($field) .']"
					        id="input'. ucfirst(strtolower($table)) . ucfirst(strtolower($field)) .'"';
			       foreach($attributes as $name=>$value) {
				    $html .= ' ' . $name . '="' . $value . '"';
			       }
			       $html .= ' />';


                      // Een eventuele foutmelding wordt weergegeven
                      if ($errorMsg) {
                           $html .= '<span>'. $errorMsg .'</span>';
                      }


			       // De division (formDivPlaceholder) wordt afgesoten
			       $html .= '</div></div>';

			       // Het veld wordt in de centrate array geplaatst
			       $this -> html[ucfirst(strtolower($table))][strtolower($field)] = $html;


			  }
		     }
		     
		     
		     // De textarea velen worden gegenereerd
		     if ($type == 'textarea') {
		     	
		     	foreach($array as $field) {
		     		
		     		// De data wordt aangemaakt
		     		//$table = ucfirst(strtolower($table));
		     		$attributes = $this -> attributes[$table][$field];
		     		
		     		// De variable html wordt opnieuw aangemaakt
			        $html = '<div id="div'. ucfirst(strtolower($table)) . ucfirst(strtolower($field)) .'" class="formDivPlaceholder"><div class="formDivPlaceholderIn">';

			        // Een eventueel label wordt aangemaakt
			        if (array_key_exists('label', $attributes)) {
				    	$html .= '<label id="label'. ucfirst(strtolower($table)) . ucfirst(strtolower($field)) .'">' . $attributes['label'] . '</label>';
				    	unset($attributes['label']);
			        }
			        
			        if (array_key_exists('value', $attributes)) {
			        	$content = $attributes['value'];
			        	unset($attributes['value']);
			        } else {
			        	$content = '';
			        }
				    
			        
			        // De textarea wordt aangemaakt
			        $html .= '<textarea name="post['. ucfirst(strtolower($table)) .']['. strtolower($field) .']" ';
			        foreach($attributes as $key=>$value) {
			        	$html .= $key . '="'. $value .'" ';
			        }
			        $html .= '>';
			        $html .= $content;
			        $html .= '</textarea>';
			        
			        
				    // De divisions worden gesloden
				    $html .= '</div></div>';

				    
				    // De HTML code wordt in de HTML array gepushed
			       $this -> html[ucfirst(strtolower($table))][strtolower($field)] = $html;
			       
		       }
	     	}		     	
		     


		     // De SELECT velden worden gegenereerd
		     if ($type == 'select') {

			  foreach($array as $field) {

                      // De tabel wordt klaargemaakt
                      $table = ucfirst(strtolower($table));

			       // De attributen worden in een string geplaatst
			       $attributes = $this -> attributes[$table][$field];

			       
			       // De variable html wordt opnieuw aangemaakt
			       $html = '<div id="div'. ucfirst(strtolower($table)) . ucfirst(strtolower($field)) .'" class="formDivPlaceholder"><div class="formDivPlaceholderIn">';

			       // Een eventueel label wordt aangemaakt
			       if (array_key_exists('label', $attributes)) {
				    $html .= '<label id="label'. ucfirst(strtolower($table)) . ucfirst(strtolower($field)) .'">' . $attributes['label'] . '</label>';
				    unset($attributes['label']);
			       }

			       // De attributen worden in een string gezet
			       $attributes = $this -> attributes[$table][$field];

			       
			       // Het select element wordt aangemaakt
			       $html .= '<select name="post['. ucfirst(strtolower($table)) .']['. strtolower($field) .']" id="input'. ucfirst(strtolower($table)) . ucfirst(strtolower($field)) .'">';

			       // Er wordt bekeken of auto_select van toepassing is
			       if (array_key_exists('auto_select', $attributes)) {
				   $info = $attributes['auto_select'];

				    // De opties worden ingeladen
				    $query = "SELECT `". safe($info[1]) ."`, `". safe($info[2]) ."` FROM `". safe($info[0]) ."`";
				    $result = plugin::query($query);

				    $options = '';

				    while($option = mysql_fetch_assoc($result)) {
				    	$attributes['options'][$option[$info1]] = $option[$info[2]];
				    }

			       }
			       
			       if (array_key_exists('options', $attributes) && count($attributes['options']) > 0) {

			       		if ($this -> model && isset($this -> model -> data[ucfirst(strtolower($table))][$field])) {
			       			$id = $this -> model -> data[ucfirst(strtolower($table))][$field];			       			
			       		} else {
			       			$id = false;
			       		}
			       	
			       		foreach($attributes['options'] as $optKey=>$optVal) {
			       			
			       			if ($id == $optKey) {
			       				$html .= '<option value="'. $optKey .'" selected>'. $optVal .'</option>';
			       			} else {			       			
			       				$html .= '<option value="'. $optKey .'">'. $optVal .'</option>';
			       			}
			       		}
			       	
			       }


			       // Het selectiemenu wordt afgesloten
			       $html .= '</select>';

			       // Het division element wordt afgesloten
			       $html .= '</div></div>';

			       // De HTML code wordt in de HTML array gepushed
			       $this -> html[ucfirst(strtolower($table))][strtolower($field)] = $html;
			  }

		     }

		}

	    }
	}

    }


    public function setAttribute($option, $array) {

         foreach($array as $table=>$fields) {

              // De tabelnaam wordt juist geconverteerd
              $table = ucfirst(strtolower(convert_table_name($table, 'join')));

              foreach($fields as $name=>$value) {

                   $name = strtolower($name);

                   if (isset($this -> attributes[$table][$name])) {
                        $this -> attributes[$table][$name][$option] = $value;
                   }
              }

         }

    }


    public function useOnly($array) {

	foreach($array as $table=>$fields) {
	    $table = ucfirst(strtolower(convert_table_name($table, 'join')));

	    $this -> useOnly[$table] = $fields;

         // De position tabel wordt aangemaakt indien deze niet bestaat
         if (!$this -> order | empty($this -> order)) {

              foreach($fields as $field) {
                    $this -> order[$table][] = $field;
              }

         }
	}

    }


    public function ignore($array) {

	// De geselecteerde velden worden toegevoegd aan de ignore array
	foreach($array as $table=>$fields) {
	    $table = ucfirst(strtolower(convert_table_name($table, 'join')));

	    $this -> ignore[$table] = $fields;
	}

    }


    public function getOutput() {

	// De prive functie prepare wordt uitgevoerd om de array in HTML te converteren
	self::prepare();

         // De return variable wordt aangemaakt
         $return = '';
	 
	 // De formulier tag wordt geöpend
	 if (isset($this -> formAttributes['start'])) {
	     $return .= '<form';
	     foreach($this -> formAttributes['start'] as $key=>$value) {
		 $return .= ' ' . $key . '="'. $value .'"';
	     }
	     $return .= '><fieldset>';
	     
	     unset($this -> formAttributes['start']);
	 }


         if (is_array($this -> order)) {
              foreach($this -> order as $table=>$fields) {

                   foreach($fields as $field) {
                        if (isset($this -> html[ucfirst(strtolower($table))][strtolower($field)])) {
                             $return .= $this -> html[ucfirst(strtolower($table))][strtolower($field)];
                             unset($this -> html[ucfirst(strtolower($table))][strtolower($field)]);
                        }
                   }
              }
         }

         // Indien er velden niet gepositioneerd stonden, worden deze weergegeven
	 if (is_array($this -> html)) {
	     foreach($this -> html as $table=>$fields) {

		  foreach($fields as $field) {
		       $return .= $field;
		  }

	     }
	 }


	 // Er wordt gekeken of het formulier al afgesloten moet worden

	 if (isset($this -> formAttributes['end'])) {

	     // Het formulier wordt afgesloten
	     $return .= '<div id="divFormSubmit" class="formDivPlachHolder"><input ';
	     foreach($this -> formAttributes['end'] as $key=>$value) {
		 $return .= ' ' . $key . '="'. $value .'"';
	     }
	     $return .= ' /></div></fieldset></form>';
	     
	 }

         // De code wordt opgeschoond
         self::cleanUp();

         // De uiteindelijke data wordt geretouneerd
         return $return;

    }


    private function cleanUp() {
         
	// De variablen worden leeggemaakt
	$fields = array('html', 'data', 'fields', 'validation', 'attributes', 'order', 'ignore', 'useOnly');

	// Alle variablen worden leeggemaakt
	foreach($fields as $field) {
	    $this -> {$field} = '';
	}

    }
}