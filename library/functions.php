<?php

	function safe($var) {
		return mysql_real_escape_string($var);
	}

     function is_post() {
          return ($_SERVER['REQUEST_METHOD'] == 'POST' ? true : false);
     }

     function url($id) {
          $url = explode("/", __URL__);
          $id = $id - 1;

          if (array_key_exists($id, $url)) {
               return $url[$id];
          } else {
               return false;
          }
     }

     function pr($array) {
          echo '<pre>';
          print_r($array);
          echo '</pre>';
     }

     function create_link($array) {
          $link = '';
          
          foreach($array as $item) {

               if ($item != 'html') {
                    $link .= $item . '/';
               } else {
                    $link = substr($link, 0, -1) . '.html';
               }
          }

          // Onbekende tekens worden uit de link gehaald
          $link = strtolower($link);
          $link = str_replace("-", "--", $link);
          $link = preg_replace("/[^a-z0-9\-\.\/]/", "-", $link);

          return __DOMAIN__ . $link;
     }

     function navigate($url, $http=true) {

          // De HTTP header wordt aangepast
          define('HTTP', 302);

          $location = ($http ? __DOMAIN__ : '');

          if (empty($url)) {
               $location = __DOMAIN__;
          } elseif(is_array($url)) {

               foreach($url as $item) {

                    if ($item != 'html') {
                         $location .= $item . '/';
                    } else {
                         $location = substr($location, 0, -1) . '.html';
                    }
               }
          }

          header("Location: " . $location);
          exit;

     }

     function convert_table_name($table, $output) {

          $result = $table;

          switch($output) {
               case "join":
                    $string = strrev($table);

                    if (substr($string, 0, 3) == 'sei') {
                         $result = substr($table, 0, -3);
                         $result .= 'y';
                    } elseif (substr($string, 0, 1) == 's') {
                         $result = substr($table, 0, -1);
                    }
               break;

               case "outer_join":

                    if ($table[strlen($table) - 1] == 'y') {
                         $result = substr($table, 0, -1);
                         $result .= 'ies';
                    } elseif ($table[strlen($table) - 1] != 's') {
                         $result = $table . 's';
                    }

		    $result = strtolower($result);
               break;

               default:
                    $result = $table;
               break;
          }

          return $result;

     }

     function calculatePrice($price) {
          $btw = read_option('btw');

          if ($btw) {
               $price = round($price + $price / 100 * $btw, 2);
          }

         return $price;
     }

     function read_option($name) {

          $query = "SELECT `value` FROM `options` WHERE `name` = '". strtolower(safe($name)) ."'";
          $result = mysql_query($query);

          if (mysql_num_rows($result) > 0) {
               $fetch = mysql_fetch_assoc($result);
               $return = $fetch['value'];

               if ($return == 'true' || $return == 'false') {
                    $return = ($return == 'true' ? true : false);
               }

               return $return;
          } else {
               return false;
          }
     }
     
     
     function set_option($name, $value) {
     	$name = strtolower(safe($name));
     	
     	// De value wordt bepaald
     	if (is_bool($value)) {
     		$value = ($value ? 'true' : 'false');
     	}
     	
     	$value = safe($value);
     	
     	$query = "SELECT `id` FROM `options` WHERE `name` = '". $name ."'";
     	$result = mysql_query($query);
     	
     	if (mysql_num_rows($result) > 0) {
     		
     		$fetch = mysql_fetch_assoc($result);
     		$id = $fetch['id'];
     		
     		$query = "UPDATE `options` SET `value` = '". $value ."' WHERE `id` = '". $id ."'";
     		mysql_query($query);
     		
     	} else {
     		
     		$query = "INSERT INTO `options` (`value`, `name`) VALUES('". $value ."', '". $name ."')";
     		mysql_query($query);
     		
     	}
     }

     function money($price) {
         return number_format($price, 2, ',', '.');
     }

     function refresh() {
          header("Location: " . __DOMAIN__ . __URL__);
     }
     
     function make_list($array) {
     	
     	$returnArray = array();
     	
     	if (is_array($array)) {
     		foreach($array as $subArray) {
     			foreach($subArray as $table => $fields) {
     				
     				$keys = array_keys($fields);
     				$returnArray[$fields[$keys[0]]] = $fields[$keys[1]];
     				
     			}
     		}
     	}
     	
     	return $returnArray;
     	
     }

?>