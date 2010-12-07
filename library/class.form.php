<?php

class form extends template {

    protected $html;
    protected $database = __DATABASE__;

    public function __construct($tpl) {
        $this -> template = $tpl;

        // Een action ID wordt aangemaakt
        if (!is_post()) {
            $this -> general_id = md5(str_shuffle(time() . date('dmyhisss')));
            $this -> image_action_id = str_shuffle(md5(date('dmyhis') . time() . rand(1000, 99999)));
        } else {
            $this -> image_action_id = $_POST['image_action_id'];
            $this -> general_id = $_POST['general_id'];
        }
    }

    public function create($action='', $method='', $enctype='', $attr='') {

        switch($enctype) {
            case "file":
                $enctype = 'multipart/form-data';
            break;

            default:
                $enctype = 'application/x-www-form-urlencoded';
            break;
        }

        $this -> html .= '<form action="" method="'. $method .'" enctype="'. $enctype .'" '. self::attr($attr) .' /><ul>';
    }

    public function input($name, $table, $attr='') {
        $this -> html .= '<input name="'. $name .'" id="txt_'. $name .'" ' . self::attr($attr) . ' type="text" />';
        self::end($table, $name);
    }

    public function password($name, $table, $attr='') {
        $this -> html .= '<input name="'. $name .'" type="password" ' . self::attr($attr) . ' />';
        self::end($table, $name);
    }

    public function title($title) {
        $this -> html .= '<h1>' . $title . '</h1>';
    }

    public function date_input($table, $name, $attr='') {
		self::input($name, $table, $attr);
        self::end($table, $name);
    }

	public function checkbox($table, $name, $attr) {

		if (array_key_exists('value', $attr) && $attr['value'] == 'true') {
			$attr['checked'] = 'checked';
			unset($attr['value']);
		}

		$this -> html .= '<input type="checkbox" name="'. $name .'" value="true" '. self::attr($attr).' />';
		self::end($table, $name);
	}

    public function textarea($table, $name, $value, $attr='') {
        $this -> html .= '<div id="txt_'. $name .'_out"><textarea name="'. $name .'" id="txt_'. $name .'" ' . self::attr($attr) . '>'. $value .'</textarea>';
        self::end($table, $name);
    }

    public function radio($table, $name, $options, $selected='') {
        foreach($options as $option) {
            $selected = (isset($selected) && $option == $selected ? 'selected' : '');
            $this -> html .= '<input type="radio" name="'. $name .'" value="'. $option .'" '. $selected .'><span>'. $option .'</span>';
        }
        self::end($table, $name);
    }

    public function image_uploader($name, $table, $action_id, $error='') {

        if (is_array($error) && count($error) > 0) {
            $error = '<img src="'. __DOMAIN__ .'images/form_alert.gif" alt="" style="float: left; margin: 4px 0 0 10px;" />';
        }

		// De opties worden ingeladen
		$options = '<table style="font: 11px arial">';

		if (isset($_SESSION['form'][$this -> general_id]['validate'][$name])) {
			$validation = explode(',', $_SESSION['form'][$this -> general_id]['validate'][$name]);

			$items = array();
			foreach($validation as $item) {
				if (preg_match("/(.*?)\((.*?)\)/", $item, $matches)) {
					//echo '<pre>'; print_r($matches); echo '</pre>';
					$items[trim($matches[1])] = $matches[2];
				}
			}


			// De matches worden vertaald naar opties voor de gebruiker
			foreach($items as $key=>$value) {

				switch($key) {
					case "min_count":
						$options .= '<tr><td height="30" width="110">Minimaal aantal afbeeldingen</td><td>'. $value .'</td></tr>';
					break;

					case "max_count":
						$options .= '<tr><td height="30" width="110">Maximaal aantal afbeeldingen</td><td>'. $value .'</td></tr>';
					break;

					case "max_size":
						$options .= '<tr><td height="30" width="110">Maximale grootte</td><td>'. $value .'</td></tr>';
					break;

					case "exact_count":
						$options .= '<tr><td height="30" width="110">Verplicht aantal afbeeldingen</td><td>'. $value .'</td></tr>';
					break;

					case "min_dim":
						$dims = explode("|", $value);
						$options .= '<tr><td height="30" width="110">Minimale afmetingen hoogte x breedte</td><td>'. $dims[0] .'x'. $dims[1] .'</td></tr>';
					break;

					case "max_dim":
						$dims = explode("|", $value);
						$options .= '<tr><td height="30" width="110">Maximale afmetingen hoogte x breedte</td><td>'. $dims[0] .'x'. $dims[1] .'</td></tr>';
					break;

					case "exact_dim":
						$dims = explode("|", $value);
						$options .= '<tr><td height="30" width="110">Exacte afmetingen hoogte x breedte</td><td>'. $dims[0] .'x'. $dims[1] .'</td></tr>';
					break;
				}

			}

		}

		$options .= '</table>';

        $this -> html .= '<li><div style="width: 600px; height: 300px; background: #bad9eb; margin: 10px 0 0 0;">
                          '. $error .'<span style="float: left; font: bold 12px arial; color: #323232; margin: 4px 0 0 10px;">Afbeelding uploaden</span>

						<div>
							<iframe src="'. __DOMAIN__ .'single/admin_upload/'. $table .'/'. $action_id .'/" frameborder="0" style="float: left; overflow-x: hidden; width: 400px; height: 260px; border: 0; margin: 10px 0 0 10px;" /></iframe>

							<div style="float: left; width: 170px; height: 260px; background: #ffffff; margin: 10px 0 0 8px">
								<span style="font: 12px arial; margin: 2px 0 0 4px"><b>Afbeelding eisen</b></span>
								'. $options .'
							</div>
						</div>

                          </div></li>';

    }

    public function hidden($name, $value) {
        $this -> html .= '<input type="hidden" name="'. $name .'" value="'. $value .'" />';
    }

    public function selection($table, $name, $options, $selected='') {
        $this -> html .= '<select name="'. $name .'" '. self::attr($attr) .'>';
        foreach($options as $key=>$value) {
            $sl = '';
            if ($key == $selected) {
                $sl = 'SELECTED';
            }
            $this -> html .= '<option value="'. $key .'" '. $sl .'>'. $value .'</option>';
        }
        $this -> html .= '</select>';
        self::end($table, $name);
    }

    public function close($button) {
        self::hidden('general_id', $this -> general_id);
        $this -> html .= '<li><input type="submit" value="'. $button .'" /></li></ul></form>';
    }

    public function end($table, $name) {
        $err = '';

        if (isset($_SESSION['errors'][$this -> general_id][$table][$name]) && count($_SESSION['errors'][$this -> general_id][$table][$name]) > 1) {
            foreach ($_SESSION['errors'][$this -> general_id][$table][$name] as $error) {
                if (isset($_SESSION['errors'][$this -> general_id][$table][$name]['value']) && $error != $_SESSION['errors'][$this -> general_id][$table][$name]['value']) {
                    $err .= $error . '<br />';
                }
            }

            $this -> html .= '<p id="p_'.  $this -> open_label .'">'. $err .'</p>';
        } else {
            $this -> html .= '<p id="p_'. $this -> open_label .'" style="display: none;"></p>';
        }
        $this -> html .= '</li>';
    }

    public function edit($table, $id, $validate, $ajax=false) {

        if (!mysql_select_db('information_schema')) {
            die('Kon niet switchen naar database information_schema');
        }
        else {

			$this -> action = 'edit';

            // De Query wordt uitgevoerd
            $query = "SELECT `COLUMN_NAME` FROM `COLUMNS` WHERE `TABLE_NAME` = '". addslashes($table) ."' AND `table_schema` = '". addslashes($this -> database) ."'";
            $result = mysql_query($query);

            // Er wordt gekeken of de opgegeven tabel bestaat
            if (mysql_num_rows($result) < 1) {
                $this -> template -> sessionFlash('succeed', 'De opgegeven tabel bestaat niet', true);

                navigeer(url(1) . '/' . url(2) . '/');
            }
            else {

                // De ajax instelling wordt vastgelegd
                $this -> ajax = ($ajax ? true : false);
                $this -> validate = $validate;

                // De validatie array wordt in een sessie geplaatst
                if (is_array($validate)) {
                    $_SESSION['form'][$this -> general_id]['validate'] = $validate;
                }

				// De tabel wordt in de array geplaatst
				$_SESSION['form'][$this -> general_id]['table'] = $table;

                // Alle velden worden ingelezen
                if (!is_post()) {

                    // Er wordt geswitched naar de juiste database
                    mysql_select_db($this -> database);

                    // Alle velden worden opgehaald voor de check
                    $query = "SELECT * FROM `". addslashes($table) ."` WHERE `id` = '". addslashes($id) ."'";
                    $result1 = mysql_query($query);

                    // Er wordt bekeken of het opgegeven ID bestaat
                    if (mysql_num_rows($result1) < 1) {
                        $this -> template -> sessionFlash('error', 'Het opgegeven ID bestaat niet', true);

                        navigeer(url(1) . '/' . url(2) . '/');
                    }
                    else {

                        // Het edit ID wordt in een sessie gezet
                        $_SESSION['form'][$this -> general_id]['edit']['id'] = $id;

                        // De array wordt gefetched
                        $fetch = mysql_fetch_assoc($result1);

                    }

                    $values = array('form_action_edit');

                    // Alle huidige waardes worden ingeladen
                    while($var = mysql_fetch_assoc($result)) {
                        if ($var['COLUMN_NAME'] == 'image') {

                            if (!empty($fetch[$var['COLUMN_NAME']])) {
                                $images = explode(',', $fetch[$var['COLUMN_NAME']]);
                                foreach($images as $img_arr) {
                                    $img_arr1 = explode('|', $img_arr);
                                    $img_arr = array();
                                    $img_arr[$img_arr1[0]] = $img_arr1[1];

                                    foreach($img_arr as $img_id=>$ext) {
                                        $query2 = "SELECT * FROM `upload_buffer` WHERE `id` = '". $img_id ."'";
                                        $result2 = mysql_query($query2);

                                        if (mysql_num_rows($result2) > 0) {
                                            $query3 = "DELETE FROM `upload_buffer` WHERE `id` = '". $img_id ."'";
                                            mysql_query($query3);

                                            if (file_exists(__DATA__ . 'public/uploads/buffer/' . $img_id . '/image.' . $ext)) {
                                                verwijder(__DATA__ . 'public/uploads/buffer/' . $img_id);
                                            }
                                        }

                                        mysql_query("INSERT INTO `upload_buffer` VALUES('". $img_id ."', '". $ext ."', '". $this -> image_action_id ."', NOW())") or die (mysql_error());

                                        mkdir(__DATA__ . 'public/uploads/buffer/' . $img_id);
                                        copy(__DATA__ . 'public/uploads/images/'. $img_id .'/image.' . $ext, __DATA__ . 'public/uploads/buffer/' . $img_id . '/image.' . $ext);

                                    }
                                }
                            }
                        } else {
                            $values[$var['COLUMN_NAME']] = stripslashes($fetch[$var['COLUMN_NAME']]);
                        }
                    }

                    self::generate($table, $values);


                } else {
                    $valid = self::validate($table, $validate);

                    if ($valid) {
                        mysql_select_db('information_schema');

                        $extra = array();
                        $query = "SELECT `COLUMN_NAME` FROM `columns` WHERE `table_schema` = '". addslashes($this -> database) ."' AND `table_name` = '". addslashes($table) ."'";
                        $result = mysql_query($query);

                        mysql_select_db($this -> database);

                        while($var = mysql_fetch_assoc($result)) {
                            $name = $var['COLUMN_NAME'];

                            if (strtolower($name) == 'modified') {
                                $extra['modified'] = 'NOW()';
                            }

                            if (strtolower($name) == 'image') {
                                $extra['image'] = buffer_accept($this -> image_action_id, $validate);
                            }
                        }


                        // De Query wordt klaargemaakt
                        $query_fields = '';
                        foreach($_SESSION['type'][$this -> general_id][$table] as $veld=>$val) {
                            if ($veld == 'password') {
                                $password .= make_pwd($_POST[$veld]);
                                $query_fields .= "`" . $veld . "` = '" . mysql_real_escape_string($password) . "', ";
                            } elseif ($val == 'true_or_false') {
								$val = ($_POST[$veld] == 'true' ? 'true' : 'false');
								$query_fields .= "`" . $veld . "` = '" . mysql_real_escape_string($val) . "', ";
							} else {
                                $query_fields .= "`" . $veld . "` = '" . mysql_real_escape_string($_POST[$veld]) . "', ";
                            }
                        }

                        foreach($extra as $key=>$val) {
                            $query_fields .= "`" . $key . "` = ". $val . ', ';
                        }

                        // De Query wordt uitgevoerd
                        mysql_select_db($this -> database);
                        $query = "UPDATE ". addslashes($table) ." SET ". substr($query_fields, 0, -2) ." WHERE `id` = '". addslashes($id) ."'";
                        $result = mysql_query($query);

                        if ($result) {
                            $this -> template -> sessionFlash('succeed', 'Het item is succesvol aangepast.', true);
                        } else {
                            $this -> template -> sessionFlash('error', 'Het item is niet aangepast vanwege een database fout.');
                        }

                        navigeer(url(1) . '/' . url(2) . '/');
                    }
                }
            }
        }

    }

    public function delete($table, $id) {
        if (ctype_digit($id)) {

			$this -> action = 'delete';

            $query = "DELETE FROM `". safe($table) ."` WHERE `id` = '". safe($id) ."'";
            $result = mysql_query($query, LINK) or die (mysql_error());

            if ($result && mysql_affected_rows(LINK) > 0) {
                $this -> template -> sessionFlash('succeed', 'Het item is succesvol verwijderd');
            } else {
                $this -> template -> sessionFlash('error', 'Het item kon niet verwijderd worden');
            }

        } else {
            $this -> template -> sessionFlash('error', 'Het door uw opgegeven ID bestaat niet');
        }

        $url = explode('/', __URL__);
        $key = array_search('delete', $url);

        $link = '';
        for($i=0;$i<$key;++$i) {
            $link .= $url[$i] . '/';
        }

        navigeer($link);
    }

    public function generate($table, $gen_values='', $gen_errors='', $close=true) {

        if (!mysql_select_db('information_schema')) {
            die('Kon niet switchen naar database information_schema');
        } else {
            if ($close) {
                self::create('', 'post', 'file');
            }
            $_SESSION['form'][$this -> general_id][$table] = array();
            unset($_SESSION['form'][$this -> general_id]['type'][$table]);

            $query = "SELECT `COLUMN_KEY`, `DATA_TYPE`, `COLUMN_NAME`, `COLUMN_TYPE`, `CHARACTER_MAXIMUM_LENGTH`, `COLUMN_COMMENT` FROM `COLUMNS` WHERE `TABLE_NAME` = '". addslashes($table) ."' AND `table_schema` = '". addslashes($this -> database) ."'";
            $result = mysql_query($query);

            if (mysql_num_rows($result) < 1) {
                die('Database tabel '. $table .' niet gevonden');
            } else {

                $fields = array();
                $converter = array( 'input' => array('varchar', 'char', 'int', 'smallint', 'decimal'),
                                    'textarea' => array('tinytext', 'text', 'mediumtext', 'longtext'),
                                    'radio' => array('enum'),
                                    'datum' => array('date'),
                                    'radio' => array('enum'));

                mysql_select_db($this -> database);
                while($var = mysql_fetch_assoc($result)) {
                    $key = $var['COLUMN_KEY'];
                    $type = $var['DATA_TYPE'];
                    $name = $var['COLUMN_NAME'];
                    $maxlength = $var['CHARACTER_MAXIMUM_LENGTH'];
                    $label = $var['COLUMN_COMMENT'];


                   $output = false;
                    foreach($converter as $k=>$v) {

						// De decimal check wordt aangeroepen
						if ($type == 'decimal') {

							if (!isset($_SESSION['form'][$this -> general_id]['validate'][$name])) {
								$_SESSION['form'][$this -> general_id]['validate'][$name] = 'decimal';
							} else {
								$_SESSION['form'][$this -> general_id]['validate'][$name] .= ', decimal';
							}

						}

						// De integer check wordt aangeroepen
						$integer_types = array('smallint', 'tinyint', 'int', 'mediumint');
						if (in_array($type, $integer_types)) {

							if (!isset($_SESSION['form'][$this -> general_id]['validate'][$name])) {
								$_SESSION['form'][$this -> general_id]['validate'][$name] = 'integer';
							} else {
								$_SESSION['form'][$this -> general_id]['validate'][$name] .= ', integer';
							}

						}


						// De checkbox wordt aangemaakt
						if ($type == 'enum') {
							preg_match("/enum\((.*?)\)/", $var['COLUMN_TYPE'], $matches);
							$matches = preg_replace("/\'|\"/", "", $matches[1]);
							$matches = explode(',', $matches);

							if (count($matches) === 2 && in_array('true', $matches)) {
								$output = 'checkbox';
								$type = 'none';
								$_SESSION['type'][$this -> general_id][$table][$name] = 'true_or_false';
							}
						}


                        if ($label == 'none' || $name == 'id') {
                            // Doe niks
                        } elseif ($type == 'smallint' || $type == 'tinyint' || $type == 'int') {
                            $output = 'input';
                            $fields[$name]['style'] = 'width: 60px;';
                            $_SESSION['form'][$this -> general_id][$table][] = $name;
                            $_SESSION['type'][$this -> general_id][$table][$name] = $k;
                        } elseif ($name == 'password') {
                            $output = 'password';
                            $_SESSION['form'][$this -> general_id][$table][] = $name;
                            $_SESSION['type'][$this -> general_id][$table][$name] = $k;
                        } elseif (in_array($type, $v) && $name != 'image') {
                            $output = $k;
                            $_SESSION['form'][$this -> general_id][$table][] = $name;
                            $_SESSION['type'][$this -> general_id][$table][$name] = $k;
                        } elseif (preg_match('/_id/', $name) && !in_array($name, $_SESSION['form'][$this -> general_id][$table])) {
                            $_SESSION['form'][$this -> general_id][$table][] = $name;
                            $_SESSION['type'][$this -> general_id][$table][$name] = 'selection';
                        } elseif ($name == 'image') {
                            $_SESSION['form'][$this -> general_id][$table][] = $name;
                            $_SESSION['type'][$this -> general_id][$table][$name] = 'image';
                        }
                    }

                    // Primaire sleutels worden verwijderd
                    if ($key == 'PRI') {
                        $output = false;
                    }


                    if (is_array($gen_errors)) {
                        if (array_key_exists($name, $gen_errors) && count($gen_errors[$name]) > 0) {
                            $fields[$name]['style'] = '';

                            if (isset($gen_errors[$name]['value'])) {
                                $fields[$name]['value'] = $gen_errors[$name]['value'];
                            }
                        } else {
                            if (is_array($gen_values)) {
                                $fields[$name]['value'] = $gen_values[$name];
                            }

                            //unset($gen_errors[$name]);
                        }
                    }

                    if ($label == 'none') {
                        $output = '';
                    }

                    if (preg_match('/_id/', $name)) {
                        $output = 'auto_select';
                    }

                    if ($name == 'image') {
                        $output = 'image';
                        setcookie('action_id', 'test');

                        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                            $image_action_id = $this -> image_action_id;

                            if (is_array($this -> validate) && !in_array('image', $this -> validate)) {
                                $opts = array();
                                $img_opts = explode(',', $this -> validate['image']);
                                foreach($img_opts as $img) {
                                    $opts[] = trim($img);
                                }

                                $_SESSION['upload'][$this -> image_action_id] = $opts;
                            }
                        } else {
                            $image_action_id = $_POST['image_action_id'];
                        }

                        self::hidden('image_action_id', $this -> image_action_id);
                    }

                    if (isset($gen_errors[$name])) {
                        $errn = $gen_errors[$name];
                    } else {
                        $errn = '';
                    }

                    if (is_array($gen_values) && in_array('form_action_edit', $gen_values)) {
                        $fields[$name]['value'] = $gen_values[$name];
                    }

                    if ($this -> ajax) {
                        $fields[$name]['onblur'] = "form_validate('". __JSROOT__ ."lib/form_validate/" . $table . "/" . $name . "/" . $this -> general_id . "/', this);";
                    }

                    switch($output) {
                        case "input":
                            self::label($label, $name, $errn);
                            $fields[$name]['maxlength'] = $maxlength;
                            self::input($name, $table, $fields[$name]);
                        break;

                        case "password":
                            self::label($label, $name, $errn);
                            self::password($name, $table, $field[$name]);
                        break;

                        case "auto_select":
                            self::label($label, $name, $errn);
                            $selected = '';
                            if (isset($gen_values[$name])) {
                                $selected = $gen_values[$name];
                            }
                            self::selection($table, $name, self::get_selection($name), $selected);
                        break;

                        case "radio":
                            self::label($label, $name, $errn);
                            $values = explode(',', str_replace('enum(', '', str_replace(')', '', str_replace("'", '', $var['COLUMN_TYPE']))));
                            $fields[$name]['options'] = $values;
                            self::radio($table, $name, $fields[$name]['options']);
                        break;

                        case "datum":
                            self::label($label, $name, $errn);
                            self::date_input($table, $name, $fields[$name]);

							$this -> template -> plugin('datepicker');
							$this -> template -> newBlock('javascript_block');
							$this -> template -> assign('script', '$("#txt_'. $name .'").datepicker({
								changeMonth: true,
								changeYear: true,
								dateFormat: "yy-mm-dd",
								buttonText: "kiezen"
								});
								$("#txt_'. $name .'").datepicker( "option", "dayNames", [\'Dimanche\', \'Lundi\', \'Mardi\', \'Mercredi\', \'Jeudi\', \'Vendredi\', \'Samedi\'] );');
                        break;

                        case "textarea":
                            self::label($label, $name, $errn);
                            $value = '';
                            if (isset($fields[$name]['value'])) {
                                $txt_value = $fields[$name]['value'];
                                unset($fields[$name]['value']);
                            }
                            if (is_array($gen_values) && array_key_exists($name, $gen_values)) {
                                $txt_value = $gen_values[$name];
                            }

                            self::textarea($table, $name, $txt_value, $fields[$name]);
                        break;

                        case "image":
                            $img_error = '';
                            if (is_array($gen_errors) && isset($gen_errors[$name]) && count($gen_errors[$name]) > 0) {
                                $img_error = $gen_errors[$name];
                            }
                            self::image_uploader($name, $table, $this -> image_action_id, $img_error);
                        break;

						case "checkbox":
							self::label($label, $name, $errn);
							self::checkbox($table, $name, $fields[$name]);
						break;

                        case "default":

                        break;

                    }

                    if ($convert == 'auto_select') {

                        $to_table = str_replace('_id', '', $name);
                        $query1 = "SELECT `id`, `name` FROM `". addslashes($to_table) ."`";
                        $result1 = mysql_query($query);

                    }
                }

                if ($close) {
                    self::close('Aanvraag verzenden');
                }

                if (is_array($gen_errors) && count($gen_errors) > 0) {
                    $this -> template -> javascript('qtip.min');

                    foreach($gen_errors as $key=>$val) {
                        if (count($val) > 0) {
                            $to_output = '';

                            foreach($val as $err) {
                                $to_output .= $err . '<br />';
                            }

                            $this -> html .= '<div id="form_error_'. $key .'" style="display: none;">'. $to_output .'</div>';
                        }
                    }
                }
            }

        }

    }

    public function label($value, $field, $error='') {
        if (is_array($error) && count($error) > 1) {
            $total = '';
            foreach($error as $err) {
                $total .= $err . '<br />';
            }

            $this -> html .= '<li id="li_'. $field .'" style="background: #FBE3E4; border: 2px solid #FBC2C4;"><label for="'. $field .'">
                <img src="'. __DOMAIN__ . 'images/system/check.gif" id="img_'. $field .'" style="display: none; float: left; margin: 0 7px 0 0;" />'. $value .'</label>';
        } elseif (is_array($error) && count($error) < 2) {
            $this -> html .= '<li id="li_'. $field .'"><label for="'. $field .'"><img src="'. __DOMAIN__ . 'images/system/check.gif" id="img_'. $field .'" style="display: block; float: left; margin: 0 7px 0 0;" />'. $value .'</label>';
        } else {
            $this -> html .= '<li id="li_'. $field .'"><label for="'. $field .'"><img src="'. __DOMAIN__ . 'images/system/check.gif" id="img_'. $field .'" style="display: none; float: left; margin: 0 7px 0 0;" />'. $value .'</label>';
        }

        $this -> open_label = $field;
    }

    public function attr($attr) {
        $return = '';
        if (is_array($attr)) {
            foreach($attr as $key => $value) {
                $return .= $key . '="' . $value . '" ';
            }

            return $return;
        }
    }

    protected function get_selection($table) {
        $table = str_replace('_id', '', $table);

        $query = "SELECT `id`, `name` FROM `". addslashes($table) ."`";
        $result = mysql_query($query);

        if (!$result) {
            die('De tabel ' . $table . ' voor de automatische selectie kon niet worden gevonden');
        } else {

            $return = array();
            while($var = mysql_fetch_assoc($result)) {
                $return[$var['id']] = $var['name'];
            }

            return $return;

        }
    }

    public function add($table, $validation, $ajax=false) {

        mysql_select_db('INFORMATION_SCHEMA');
        $query = "SELECT * FROM `columns` WHERE `TABLE_NAME` = '". addslashes($table) ."'";
        $result = mysql_query($query);

        if (mysql_num_rows($result) < 1) {
            die('Toevoegen mislukt, database tabel ' . $table . ' werd niet gevonden!');
        } else {

            $this -> ajax = ($ajax ? true : false);
            $this -> validate = $validation;

			$this -> action = 'add';

            if (is_array($validation)) {
                $_SESSION['form'][$this -> general_id]['validate'] = $validation;
            }

            if (!is_post()) {
                self::generate($table);
            } else {
                $valid = true;
                if (is_array($validation)) {
                    $_SESSION['form'][$this -> general_id]['validate'] = $validation;
                    $valid = self::validate($table, $validation);
                }

                if ($valid) {
                    mysql_select_db('information_schema');

                    $extra = array();
                    $query = "SELECT `COLUMN_NAME` FROM `columns` WHERE `table_schema` = '". addslashes($this -> database) ."' AND `table_name` = '". addslashes($table) ."'";
                    $result = mysql_query($query);

                    mysql_select_db($this -> database);
                    while($var = mysql_fetch_assoc($result)) {
                        $name = $var['COLUMN_NAME'];

                        if (strtolower($name) == 'created') {
                            $extra['created'] = 'NOW()';
                        }

                        if (strtolower($name) == 'image') {
                            unset($_SESSION['type'][$this -> general_id][$table]['image']);
                            $extra['image'] = buffer_accept($this -> image_action_id, $validation);
                        }
                    }


                    // De Query wordt klaargemaakt
                    $exceptions = array('password');
                    $query_fields = '';
                    $query_values = '';

                    foreach($_SESSION['type'][$this -> general_id] as $velden) {
                        foreach($velden as $veld=>$val) {
                            $query_fields .= '`' . $veld . '`, ';

                            if (in_array($veld, $exceptions)) {
                                switch($veld) {
                                    case "password":
                                        $password .= make_pwd($_POST[$veld]);
                                        $query_values .= "'". mysql_real_escape_string($password) ."', ";
                                    break;
                                }
                            } else {
                                $query_values .= "'" . mysql_real_escape_string($_POST[$veld]) . "', ";
                            }
                        }
                    }

                    foreach($extra as $key=>$val) {
                        $query_fields .= '`' . $key . '`, ';
                        $query_values .= $val . ', ';
                    }

                    // De Query wordt uitgevoerd
                    mysql_select_db($this -> database);
                    $query = "INSERT INTO `". addslashes(trim($table)) ."` (". substr($query_fields, 0, -2) .") VALUES (". substr($query_values, 0, -2) .")";
                    $result = mysql_query($query) or die (mysql_error());


                    if ($result) {
                        $this -> template -> sessionFlash('succeed', 'Het item is succesvol toegevoegd.');
                    } else {
                        $this -> template -> sessionFlash('error', 'Het item is niet toegevoegd vanwege een database fout.');
                    }

                    navigeer(url(1) . '/' . url(2). '/');
                }
            }
        }

    }


    public function validate($table, $array) {

        if (!is_post()) {
            self::generate($table);
        } else {

            // De variablen worden aangemaakt
            $rsn = array();
            $gen_values = array();

            // De database wordt geselecteerd
            mysql_select_db($this -> database);

            // Er wordt gecontroleerd of de POST wel is toegestaan
            if ($_SESSION['form'][$this -> general_id][$table]) {
                $total_fields = $_SESSION['form'][$this -> general_id][$table];
            } else {
                die('De formulier sessie is niet gestart, controleer of uw cookies aanstaan!');
            }

            foreach($array as $name=>$raw) {

                // De velden worden geëxploded
                $outp = explode(',', $raw);

                foreach($outp as $check) {

                    // De velden worden voorbereid
                    $check = trim($check);
                    $all_fields = ($name == 'all' ? $total_fields : array($name));

                    foreach($all_fields as $field_name) {
                        if (!isset($rsn[$field_name])) {
                            $rsn[$field_name] = array();

                            // De value wordt aangemaakt
                            if (!array_key_exists($field_name, $gen_values)) {
                                $gen_values[$field_name] = $_POST[$field_name];
                            }
                        }

                        if(in_array($field_name, $total_fields)) {
                            $rsn[$field_name]['value'] = $_POST[$field_name];

                            // Het resultaat wordt bekeken
                            if (isset($_SESSION['form'][$this -> general_id]['edit']['id'])) {
                                $edit_id = $_SESSION['form'][$this -> general_id]['edit']['id'];
                            } else {
                                $edit_id = false;
                            }

                            $result = self::validate_field($table, $field_name, $check, $_POST[$field_name], $edit_id);

                            if ($result) {
                                $rsn[$field_name][] = $result;
                            }
                        }
                    }
                }
            }


            // Er wordt bekeken of er fouten zijn gevonden tijdens de validatie
            $valid = 0;
            foreach($rsn as $rs) {
                if (count($rs) > 0) {
                    $valid = $valid + count($rs) - 1;
                }
            }


            // Indien er fouten zijn, wordt een error-flash weergegeven
            if ($valid > 0) {
                $_SESSION['errors'][$this -> general_id][$table] = $rsn;
                self::generate($table, $gen_values, $rsn);
                $this -> template -> setFlash('error', 'U heeft een aantal velden incorrect ingevuld!');
                return false;
            } else {
                return true;
            }

        }
    }


    public function validate_field($table, $field_name, $check, $value, $edit_id=false) {

        // Alle foutmeldingen
        $err1 = 'Dit is een verplicht veld';
        $err2 = 'Er bestaat al een veld met dezelfde waarde';
        $err3 = 'Verplicht minimaal aantal tekens: ';
        $err4 = 'Maximaal aantal toegestane tekens: ';
        $err5 = 'U heeft een onjuist e-mail adres opgegeven';

        $error = false;

        switch($check) {
            case "not_empty":
                if ($field_name != 'image') {
                    if (!isset($value) || empty($value)) {
                        $error = $err1;
                    }
                }
            break;

            case "unique":
                $query = "SELECT * FROM `". addslashes($table) ."` WHERE LOWER(`". addslashes($field_name) ."`) = '". mysql_real_escape_string(trim(strtolower($value))) ."'";
                $result = mysql_query($query);

                if ($edit_id) {
                    $query = "SELECT `". $field_name ."` FROM `". $table ."` WHERE `id` = '". $edit_id ."'";
                    $fetch = mysql_fetch_assoc(mysql_query($query));

                    if (strtolower(trim($fetch[$field_name])) != strtolower(trim($value))) {
                        $query = "SELECT * FROM `". $table ."` WHERE LOWER(`". $field_name ."`) = '". mysql_real_escape_string(strtolower(trim($value))) ."'";
                        $result = mysql_query($query);

                        if (mysql_num_rows($result) > 0) {
                            $error = $err2;
                        }
                    }

                } elseif (mysql_num_rows($result) > 0) {
                    $error = $err2;
                }
            break;

            case "aplhanumeric":
                if (!preg_match("/^[a-zA-Z0-9 \-]+$/", $value)) {
                    $error = 'Alleen de volgende tekens zijn toegestaan: <b>a-z 0-9 -</b>';
                }
            break;

            case "email":
                if (isset($value) && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $value)) {
                    $error = $err5;
                }
            break;

            default:
                if (preg_match("/min\((.*?)\)/i", $check, $matches) && $field_name != 'image') {
                    $number = $matches[1];

                    if (!ctype_digit($number)) {
                        die('Ongeldig getal ingevoerd in de validatie van ' . $field_name);
                    } else {
                        if (strlen($value) < $number && $_SESSION['type'][$this -> general_id][$table][$field_name] != 'selection') {
                            $error = $err3 . $number;
                        }
                    }
                }

                if (preg_match("/max\((.*?)\)/", $check, $matches) && $field_name != 'image') {
                    $number = $matches[1];

                    if (!ctype_digit($number)) {
                        die('Ongeldig getal ingevoerd in de validatie van ' . $field_name);
                    } else {
                        if (strlen($value) > $number) {
                            $error = $err4 . $number;
                        }
                    }
                }

                if (preg_match('/exact_count\((.*?)\)/', $check, $matches) && $field_name == 'image') {
                    $image_action_id = $_POST['image_action_id'];

                    $query = "SELECT * FROM `upload_buffer` WHERE `action_id` = '". safe($image_action_id) ."'";
                    $result = mysql_query($query);

                    if (mysql_num_rows($result) != $matches[1]) {
                        $error = 'U dient exact ' . $matches[1] . ' afbeeldingen te uploaden!';
                    }
                } elseif (preg_match('/min_count\((.*?)\)/', $check, $matches) && $field_name == 'image') {
                    $image_action_id = $_POST['image_action_id'];

                    $query = "SELECT * FROM `upload_buffer` WHERE `action_id` = '". safe($image_action_id) ."'";
                    $result = mysql_query($query);

                    if (mysql_num_rows($result) < $matches[1]) {
                        $error = 'U dient minimaal ' . $matches[1] . ' afbeeldingen te uploaden';
                    }
                }
            break;
        }

        // De fout status wordt geretouneerd
        return ($error ? $error : false);
    }


    public function overview($title, $table) {

        // Switch database
        mysql_select_db('information_schema', LINK);

        // Execute Query
        $query = "SELECT `COLUMN_NAME`, `DATA_TYPE`, `COLUMN_COMMENT` FROM `COLUMNS` WHERE `TABLE_NAME` = '". safe($table) ."' ORDER BY `ordinal_position` ASC";
        $result = mysql_query($query, LINK);

        if (mysql_num_rows($result) < 1) {
            die('De door uw opgegeven tabel bestaat niet, overview error');
        } else {

            while($var = mysql_fetch_assoc($result)) {
                if ($var['DATA_TYPE'] == 'varchar' || $var['DATA_TYPE'] == 'char' && $var['COLUMN_COMMENT'] != 'none') {
                    $field = $var['COLUMN_NAME'];
                    $label = $var['COLUMN_COMMENT'];
                    break;
                }
            }

            mysql_select_db($this -> database, LINK);
            $query = "SELECT `id`, `". $field ."` as name FROM `". safe($table) ."` LIMIT 0,10";
            $result = mysql_query($query, LINK);

            // Het block wordt aangeroepen
            $this -> template -> javascript('form');
            $this -> template -> newBlock('form_overview');
            $this -> template -> assignGlobal( array('title' => $title, 'url' => __DOMAIN__ . __URL__, 'table' => $table, 'label' => $label) );

            // Het block wordt opgevuld
            while($var = mysql_fetch_assoc($result)) {
                $this -> template -> newBlock('form_item');
                $this -> template -> assign( $var);

                if (isset($i) && $i == '1') {
                    $this -> template -> assign('class', 'ul1');
                    $i = 0;
                } else {
                    $i = 1;
                }

            }

        }

    }

    public function auto($request, $table, $validate, $name, $id='', $ajax=false) {

        switch($request) {
            case "add":
                self::title($name . ' toevoegen');
                self::add($table, $validate, $ajax);
            break;

            case "edit":
                self::title($name . ' bewerken');
                self::edit($table, $id, $validate, $ajax);
            break;

            case "delete":
                self::delete($table, $id);
            break;

            default:
                self::overview($name, $table);
            break;
        }

    }

	public function editor($field, $width, $height, $margin, $toolbar='Full', $skin='kama', $uicolor='#dee5f8', $resize_enabled=false) {

		$this -> template -> javascript('ckeditor/ckeditor');
		$this -> template -> javascript('ckeditor/adapters/jquery');

		$this -> template -> newBlock('javascript_block');
		$this -> template -> assign("script", "
			$(document).ready(function() {
				CKEDITOR.replace( 'txt_". $field ."',
				{
					toolbar: '". $toolbar ."',
					skin : '". $skin ."',
					uiColor: '". $uicolor ."',
					resize_enabled : false,
					width : ". $width .",
					height : ". $height ."
				});

				$('#txt_".$field."_out').css('margin-top', '". $margin ."px');
			});
		");

	}

	public function autoSave($time, $fields, $limit=10) {

		// Er wordt bekeken of de actie wel correct is
		if ($this -> action == 'add' OR $this -> action == 'edit') {

			// De tabel wordt opgehaald
			$table = $_SESSION['form'][$this -> general_id]['table'];

			// Het scriptblock wordt aangemaakt
			$script = 'function updateInterval() {';
			$subscript = '$(document).ready(function() {';

			// Er wordt gekeken welke velden moeten worden opgehaald
			if (is_array($fields)) {
				foreach($fields as $field) {
					$script .= "saveField('". $table ."', ". $limit .", '". $field ."', $('#txt_". $field ."').val()); ";
					$subscript .= '$("label[for='. $field .']").html( $("label[for=name]").html()+\' <a href="#" title="Formulier geschiedenis" onclick="showHistory(\\\''. $field .'\\\', \\\''. $table .'\\\');">Herstel</a>\' );';
				}
			} else {
				$script .= "saveField('". $table ."', ". $limit .", '". $fields ."', $('#txt_". $fields ."').val()); ";
				$subscript .= "$('label[for=name]').attr();";
			}

			$script .= 'setTimeout("updateInterval()", '. $time * 1000 .'); ';
			$script .= '} setTimeout("updateInterval()", '. $time * 1000 .');';

			$subscript .= '});';

			// Het script wordt in de template geplaatst
			$this -> template -> newBlock('javascript_block');
			$this -> template -> assign('script', $script);
			$this -> template -> assign('script', $subscript);

		}
	}

	public function style($array) {

		$script = '$(document).ready(function() {' . "\n";

		foreach($array as $field=>$dimensions) {
			$script .= '$("#txt_'. $field .'").css({';

			foreach($dimensions as $name=>$value) {
				$script .= "'". $name ."' : ". (is_numeric($value) ? $value : "'". $value ."'");

				if (next($dimensions)) {
					$script .= ', ';
				}
			}

			$script .= '});'. "\n";
		}

		$script .= "\n" . '});';

		$this -> template -> newBlock('javascript_block');
		$this -> template -> assign('script', $script);

	}

    public function __destruct() {
        self::toScreen();
    }

    public function toScreen() {
        if (!empty($this -> html)) {
            $this -> template -> javascript('form');
            $this -> template -> newBlock('auto_form');
            $this -> template -> assign('content', $this -> html);
        }
    }
}