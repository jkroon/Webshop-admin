<?php

class model {

    public $data = false;
    protected $table;
    public $num_rows;
    public $validateErrors = false;
    protected $validate;

    public function __construct() {
        if ($_POST) {
             $this -> data = $_POST['post'];
        }
        
        // Er wordt gekeken of er nog overige models geïncluded moeten worden
        if (isset($this -> hasMany)) {
        	foreach($this -> hasMany as $model) {
        		$name = ucfirst(strtolower(convert_table_name($model, 'join')));
        		$className = $name . '_model';

        		if (class_exists($className)) {
        			$this -> {$name} = new $className();
        		}
        	}
        }
    }
    

     public function save($array) {

          // De validatie wordt uitgevoerd
          $valid = self::validates($array);

          // Wanneer het veld niet gevalideerd is
          if (!$valid) {
               return false;
          }

          
          // Indien de functie beforeSave aanwezig is, wordt deze geladen
          if (method_exists($this, 'beforeSave')) {
         		$array = $this -> beforeSave($array);
          }
          

          // De tabellen worden bekeken
          foreach($array as $table=>$fields) {

               // De tabelnamen worden geconverteerd
               $table = convert_table_name($table, 'outer_join');
               $model_name = convert_table_name($table, 'outer_join');

               // Er wordt gekeken of er een model aanwezig is van de tabel
               if ($table == $model_name) {

                    // De tabelstructuur wordt opgehaald
                    $result = self::query("DESCRIBE `". safe($table) ."`");

                    // Er wordt gecontroleerd of de tabel bestaat
                    if ($this -> num_rows > 0) {

                         // Alle velden worden in een array gezet
                         $tableFields = array();

                         while($var = mysql_fetch_assoc($result)) {
                              if (array_key_exists($var['Field'], $fields) && !empty($fields[$var['Field']])) {
                                   $tableFields[] = $var['Field'];
                              }
                              
                              if(array_key_exists($var['Field'], $fields) && is_null($fields[$var['Field']])) {
                                   $tableFields[] = $var['Field'];
                              }
                         }


                         // Er wordt gekeken of het om een update of insert gaat
                         if (isset($fields['id'])) {
                         	
                         	// Het gaat hier om een UPDATE
                         	$query = "UPDATE `". safe($table) ."` SET ";
                         	
                         	foreach($tableFields as $field) {
                         		
                         		if (is_null($fields[$field])) {
                         			$value = 'NULL';
                         		} else {
                         			$value = "'". safe($fields[$field]) ."'";
                         		}
                         		
                         		$query .= "`". $field ."` = ". $value .", ";
                         	}
                         	
                         	$query = substr($query, 0, -2);
                         	
                         	// Het ID wordt meegegeven
                         	$query .= " WHERE `id` = '". safe($fields['id']) ."'";

                         	// De query wordt uitgevoerd
                         	$result = self::query($query);
                         	
                         	if ($result) {
                         		if (method_exists($this, 'afterSave')) {
                         			$this -> afterSave($array);
                         		}
                         	}
                         	
                         	return ($result ? true : false);

                         } else {

                              // Er wordt een INSERT query uitgevoerd
                              $query = "INSERT INTO `". safe($table) ."` (";

                              // De geselecteerde velden worden in de query geplaatst
                              foreach($tableFields as $field) {
                                   $query .= '`'. $field .'`, ';
                              }

                              // De laatste komma wordt van de query afgehaald
                              $query = substr($query, 0, -2);

                              // De opgegeven velden worden afgesloten
                              $query .= ') VALUES(';

                              // De VALUES worden in de query geplaatst
                              foreach($tableFields as $field) {
                              	
                              		if (is_null($fields[$field])) {
                              			$value = null;
                              		} else {
                              			$value = "'". safe($fields[$field]) . "'";
                              		}
                              	
                                   $query .= $value .", ";
                              }

                              // De laatste komma wordt van de query afgehaald
                              $query = substr($query, 0, -2);

                              // De VALUES worden afgesloten
                              $query .= ')';

                              $result = self::query($query);

                              if ($result) {
                              	   $this -> insert_id = mysql_insert_id();
                              	   
                              	   // De afterSave function wordt aangeroepen
                              	   if (method_exists($this, 'afterSave')) {
                              	   		$this -> afterSave($array);
                              	   }
                              }
                              
                              return ($result ? true : false);


                         }

                    }
               }
          }
     }


     public function validates($data) {
          $table = convert_table_name($this -> name, 'outer_join');

          if (isset($this -> validation)) {
               $toValidateFields = $data[$this -> name];
               $validateRules = $this -> validation;
               
               // De functie beforeValidate wordt uitgevoerd
               if (method_exists($this, 'beforeValidates')) {
               		$this -> beforeValidates($data);
               }

               // De error array wordt aangemaakt
               if (!is_array($this -> validateErrors)) {
               		$this -> validateErrors = array();
               }

               foreach($validateRules as $field=>$validation) {

                    // De variable waarin staat aangegeven of verder moet worden gegaan met de validatie
                    $do = true;

                    // De regels worden in een variable geplaatst
                    $rules = $this -> validate[$field];

                    // Er wordt bekeken of een veld verplicht is
                    if ($validation == 'required') {
                         if (!isset($toValidateFields[$field])) {
                              $this -> validateErrors[$this -> name][$field] = 'Dit is een verplicht veld';
                              $do = false;
                         }
                    }


                    // Er wordt bekeken of het veld ingevuld had moeten zijn
                    if ($do && $validation == 'notEmpty') {
                         if (empty($toValidateFields[$field])) {
                              $this -> validateErrors[$this -> name][$field] = 'Dit is een verplicht veld';
                              $do = false;
                         }
                    }


                    // De alphanumeric check wordt uitgevoerd
                    if ($do && $validation == 'alphaNumeric') {
                         if (!preg_match("/[a-zA-Z0-9\- ./", $toValidateFields[$field])) {
                              $this -> validateErrors[$this -> name][$field] = 'U mag hier enkel alfabetische en numerieke karakters invullen';
                              $do = false;
                         }
                    }


                    // De Email check wordt uitgevoerd
                    if ($do && $validation == 'email') {
                         if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $toValidateFields[$field])) {
                              $this -> validateErrors[$this -> name][$field] = 'U heeft een onjuist e-mail adres opgegeven!';
                              $do = false;
                         }
                    }


                    // De postcode check wordt uitgevoerd
                    if ($do && $validation == 'zip_code') {
                         if (!preg_match("/^[1-9]{1}[0-9]{3} {0,1}[a-zA-Z]{2}$/", $toValidateFields[$field])) {
                              $this -> validateErrors[$this -> name][$field] = 'U heeft een onjuiste postcode opgegeven';
                              $do = false;
                         }
                    }

                    // Er wordt gekeken of er fouten zijn gevonden
                    return (isset($this -> validateErrors[$this -> name]) ? false : true);

               }
          } else {
               return true;
          }
     }


    public function cache() {

    }

    public function table($table) {
        $this -> table = $table;
    }
	
	public function auth() {		
		return (isset($_SESSION['account']['username']) ? true : false);
	}

     
     public function findById($id) {
          $data = $this -> find('all', array('conditions' => array('id' => $id)));

          if ($data) {
               return $data[0];
          } else {
               return false;
          }
     }


    public function find($fields='', $conditions='') {

    	// De tabelnaam wordt gecreerd
        $table = strtolower(convert_table_name($this -> name, 'outer_join'));

        
        // Indien de tabelnaam niet leeg is zal de find functie verder gaan
        if (!empty($table)) {

        	
			// Alle velden worden geexploded
			if (empty($fields) || $fields == 'all' || $fields == '*') {
				$fields = '*';
			} else {
				$fields = explode(',', $fields);
			
				foreach($fields as $key=>$value) {
					$fields[$key] = trim($value);
				}
			}
						

			// De standaard variablen worden aangemaakt
			$order = '';
			$where = '';
			$limit = '';
            $joins = array();
            $join_opts = array();
			
			
			// De conditions en joins worden ingelezen
			if (is_array($conditions)) {
			
				foreach($conditions as $type=>$value) {
				
					switch($type) {
						case "conditions":
							$where .= "WHERE ";
						
							foreach ($value as $key=>$val) {

                                        $val = (is_null($val) ? ' IS NULL' : " = '". safe($val) ."'");

								$where .= " ". safe($key) . $val ." AND";
							}
							
							$where = substr($where, 0, -4);
						break;
						
						
						case "order":
							
							// De order by tag wordt geladen
							if (isset($value[0]) && isset($value[1])) {
								$order = " ORDER BY `". safe($value[0]) ."` " . $value[1];
							}
							
						break;
						
						
						case "limit":
						
							// Het limiet wordt ingelezen
							if (!empty($value)) {
								$limit = " LIMIT " . safe($value);
							}
						
						break;
						
						
						case "belongsTo":

							// De associatie array wordt aangeroepen
							foreach($value as $key=>$val) {
                            	
								$joins[$key] = $val;
                                $join_opts[$key] = 'belongsTo';
								
								// Indien de kolom met daarin het ID naar de assocatie nog niet is aangeroepen, wordt dit gedaan
								if (is_array($fields) && !in_array($key . '_id', $fields)) {
									$fields[] = $key . '_id';
								}
							}
						
						break;

						
						case "hasMany":
							foreach($value as $join_field=>$join_fields) {
								$joins[$join_field] = $join_fields;
								$join_opts[$join_field] = 'hasMany';
							}
						break;
					}
				
				}
			
			}
			
			
			// De velden worden opgehaald
            if (is_array($fields)) {
                $total = '';
                
                foreach($fields as $field){
                    $total .= '`'. safe(trim($field)) .'`, ';
                }
				
                $fields = substr($total, 0, strlen($total) - 2);
            }
			
			
			// De query wordt aangemaakt
			$query = "SELECT ". $fields ." FROM `". safe($table) ."` " . $where . $order . $limit;
			$result = self::query($query);

			
			// Wanneer de query niet kan worden uitgevoerd zal een error worden gereturned
            if (!$result) {
                die (mysql_error());
            }
            
            
			// Het aantal rijen wordt weergegeven
            $this -> num_rows = mysql_num_rows($result);

            
            // Een nieuwe array wordt aangemaakt
            $array = array();
            
            // De counter wordt aangemaakt
			$i = 0;
			
			
			// Indien noodzakelijk, wordt de data in gereturned
			if (isset($conditions['onlyNumRows'])) {
				return $this -> num_rows;
			}
			
			// De resultaten worden in de array geplaatst
            while($var = mysql_fetch_assoc($result)) {
            	
            	// De array wordt opgevuld met het resultaat
                $array[$i][$this -> name] = $var;
				
                
                // Indien opgegeven, worden de joins uitgevoerd
				if (isset($joins) && is_array($joins)) {

                	// De table voor de join wordt geconverteerd
                    $join_table_name = convert_table_name($table, 'join');

                    
                    // Alle joins worden uitgevoerd
					foreach($joins as $join_table=>$join_fields) {

							// De tabelnaam wordt in een nieuwe variable geplaatst
                            $join_opts_name = $join_table;
						
                            
                            // Er wordt gekeken welke relatie de tabel heeft t.o.v. de join tabel
                            if ($join_table[strlen($join_table) - 1] != 's') {
                            	$join_table = convert_table_name($join_table, 'outer_join');
                                $join_table_field = 'id';
                            } else {
                            	$join_table_field = convert_table_name($table, 'join') . '_id';
                            }

                            
                            // De waarde om te zoeken wordt bepaald
                            if ($join_opts[$join_opts_name] == 'belongsTo') {
                            	$join_table_value = convert_table_name($join_table, 'join') . '_id';
                            } else {
                            	$join_table_value = 'id';
                            }

                            
                            // Er wordt bekeken welke velden moeten worden opgehaald
							if (empty($join_fields) || $join_fields == '*' || $join_fields == 'all') {
								$subfields = '*';
							} else {
								$join_fields = explode(',', $join_fields);
								$subfields = '';
							
								foreach($join_fields as $field) {
									$subfields .= '`'. safe(trim($field)) .'`, ';
								}
							
								$subfields = substr($subfields, 0, -2);
							}
						
							
							// De query wordt uitgevoerd
							$subquery = "SELECT ". $subfields ." FROM `". safe($join_table) ."` WHERE `".$join_table_field."` = '". $var[$join_table_value] ."'";
							$subresult = self::query($subquery);

						
							// Wanneer de query succesvol is uitgevoerd, wordt de tabel aangevuld
							if ($subresult) {
							
                            	if ($join_opts[$join_opts_name] == 'belongsTo') {
                                	$array[$i][convert_table_name($join_table, 'join')] = mysql_fetch_assoc($subresult);
                                } else {
                                    $var1 = array();

                                    while($var2 = mysql_fetch_assoc($subresult)) {
                                    	$var1[] = $var2;
                                    }

                                    $array[$i][$join_table] = $var1;
                                }
							}
						}
					}

				// De counter wordt opgeteld
				++$i;
            }

            // De data wordt gereturned
            return $array;

        } else {
        	
        	// Een foutmelding wordt gereturned
            die("U heeft geen tabel opgegeven om de functie <b>find</b> uit te kunnen voeren");
            
        }

    }
	

	public function executed_queries() {
		return $this -> mysql_queries;
	}
	
	
	public function query($query) {
	
		if (!empty($query)) {

               // De soort query wordt bepaald
               $query = trim($query);
               $start = strtolower(substr($query, 0, 6));

               switch($start) {
                    case "insert":
                         $sort = 'insert';
                    break;

                    case "update":
                         $sort = 'update';
                    break;

                    case "select":
                         $sort = 'select';
                    break;

                    case "delete":
                         $sort = 'delete';
                    break;

                    default:
                         $sort = '';
                    break;
               }
			
			// De query wordt uitgevoerd
			$start = microtime(true);

			$result = mysql_query($query);
			
			$end = microtime(true);
			
			$took = substr($end - $start, 0, 7);
			
			$this -> mysql_queries[] = array(
												'query' => $query,
												'error' => (mysql_error() ? mysql_error() : false),
												'affected' => (@mysql_affected_rows($result) ? mysql_affected_rows($result) : 0),
												'rows' => @mysql_num_rows($result),
												'took' => $took
											);
			
			if (!$result) {
				return false;
			} else {
                    if ($sort == 'insert') {
                         $this -> insert_id = mysql_insert_id();
                    } elseif ($sort == 'delete') {
                         $this -> affected = mysql_affected_rows();
                    } else {
                         $this -> num_rows = @mysql_num_rows($result);
                    }
                    
				return $result;
			}
		}
	
	}
	
	
	public function delete($id) {
	
		if (is_array($id)) {
			
			// de Query wordt klaargemaakt
			$query = "DELETE FROM `". strtolower(convert_table_name($this -> name, 'outer_join')) ."` WHERE ";
			
			foreach($id as $field=>$condition) {
				$query .= "`". safe($field) ."` = '". $condition ."' AND";
			}
			
			// De AND wordt gestript van de query
			$query = substr($query, 0, -4);
			
			// De query wordt uitgevoerd
			$result = $this -> query($query);
			
			return ($result ? true : false);
			
		}
		
		if (ctype_digit($id)) {
			
			// Indien noodzakelijk, wordt de beforeDelete functie aangeroepen
			if (method_exists($this, 'beforeDelete')) {
				$this -> beforeDelete($id);
			}
			
			$query = "DELETE FROM `". strtolower(convert_table_name($this -> name, 'outer_join')) ."` WHERE `id` = '". safe($id) ."'";
			$result = $this -> query($query);
			
			if ($this -> affected > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
		
	}
	
	public function exists($table, $field, $value) {
	
		$query = "SELECT * FROM `". safe($table) ."` WHERE `". safe($field) ."` = '". safe($value) ."'";
		$result = mysql_query($query);
		
		return (!$result OR mysql_num_rows($result) < 1 ? false : true);
	
	}
    
}

?>
