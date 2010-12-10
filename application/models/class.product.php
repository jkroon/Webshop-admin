<?php

class Product_model extends model {
	
	var $name = 'Product';

    var $validation = array('name' => 'notEmpty');
	
    var $hasMany = array('product_images', 'product_options');
    
    function beforeValidates($data) {
    	
    	// Eventuele productvariaties worden getest
    	if ($data['Product']['use_options'] == 'true') {
    		
    		// De validateErrors worden aangemaakt
    		foreach($data['Product']['options'] as $key=>$array) {
    			
    			// De naam van de optie wordt gecontroleerd
    			if (isset($array['name']) && empty($array['name'])) {
    				$this -> validateErrors['Product']['options'][$key]['name'] = 'Dit is een verplicht veld';
    				unset($array['name']);
    			} elseif(isset($array['name'])) {
    				unset($array['name']);
    			}
    			
    			// De sub-opties worden gecontroleerd
    			foreach($array as $subKey=>$subArr) {
    				
    				// De naam van de suboptie wordt gecontroleerd
    				if (!isset($subArr['name']) || empty($subArr['name'])) {
    					$this -> validateErrors['Product']['options'][$key][$subKey]['name'] = 'Het veld <b>naam</b> is een verplicht veld';
    				}
    				
    				if (empty($subArr['price']) || !preg_match("/^[0-9]{1,5}(\.?[0-9]{2})?$/", $subArr['price'])) {
    					$this -> validateErrors['Product']['options'][$key][$subKey]['price'] = 'U heeft het veld <b>prijs</b> niet/incorrent ingevuld. Voorbeeld: &euro;50,00 vult u in als <b>50.00</b> of <b>50</b>';
    				}
    				
    			}
    			
    		}
    		
    	}
    	
    }
    
    
    function afterSave($data) {
    	  	
    	
    	// Er wordt bekeken of er product opties aanwezig zijn
    	if ($data['Product']['use_options'] == 'true') {
    		
        	// Alle productopties worden opgehaald
    		$result = $this -> Product_option -> find('id', array(
    			'conditions' => array(
    				'product_id' => $data['Product']['id'],
    			)
    		));
    		
    		// Alle optie ID's worden in een array gezet voor het verwijderen hiervan
    		$option_ids = array();
        	$suboption_ids = array();
        	
    		foreach($result as $obj) {
    			$option_ids[] = $obj['Product_option']['id'];
    			
	    		$result1 = $this -> Product_option -> Product_suboption -> find('id', array(
	    			'conditions' => array(
	    				'parent_id' => $obj['Product_option']['id']
	    			)
	    		));
	    		
	    		// Alle product subopties worden opgehaald
	    		foreach($result1 as $obj1) {
	    			$suboption_ids[] = $obj1['Product_suboption']['id'];
	    		}
    		}
    		

    		
    		foreach($data['Product']['options'] as $key=>$array) {

    			
    			// De product array wordt aangemaakt
    			$toSave = array(
    				'Product_option' => array(
    					'name' => $array['name'],
    					'product_id' => $data['Product']['id']
    				)
    			);
    			
    			
    			// Er wordt gekeken of de optie al bestaat
    			$result = $this -> Product_option -> find('id', array(
    				'conditions' => array(
    					'id' => $key,
    					'product_id' => $data['Product']['id']
    				)
    			));
    			    			
    			
    			// Het ID wordt uit de array gehaald om te verwijderen
    			if ($this -> Product_option -> num_rows > 0) {
    				$array_key = array_search($key, $option_ids);
    				unset($option_ids[$array_key]);
    				
    				$toSave['Product_option']['id'] = $key;
    			} else {
    				
    			}

    			// De product optie wordt opgeslagen
    			$this -> Product_option -> save($toSave);
    			
    			// De naam wordt verwijderd uit de array
    			unset($array['name']);
    			
    			
    			
    			// Alle opties worden door de loop gehaald
    			foreach($array as $key1=>$array1) {
    				
    				// De save array wordt aangemaakt
    				$toSave = array(
    					'Product_suboption' => array(
    						'name' => $array1['name'],
    						'price' => $array1['price'],
    						'type' => $array1['type'],
    						'article_id' => $array1['article_id'],
    						'parent_id' => $key
    					)
    				);
    				
    				// Er wordt gekeken of de product optie al bestaat
    				$result = $this -> Product_option -> Product_suboption -> find('id', array(
    					'conditions' => array(
    						'parent_id' => $key,
    				        'id' => $key1
    					)
    				));
    				
    				if ($this -> Product_option -> Product_suboption -> num_rows > 0) {
    					$toSave['Product_suboption']['id'] = $result[0]['Product_suboption']['id'];
    					
    					$array_key = array_search($result[0]['Product_suboption']['id'], $suboption_ids);
    					unset($suboption_ids[$array_key]);
    				}
    				
    				
    				// De opties worden opgeslagen
    				$this -> Product_option -> Product_suboption -> save($toSave);
    				
    			}
    			
    		}
    		
    		foreach($suboption_ids as $id) {
    			$this -> Product_option -> Product_suboption -> delete($id);
    		}
    		
    		
        	// Alle opties worden verwijderd
    		foreach($option_ids as $id) {
    			$this -> Product_option -> delete($id);
    		}
    		
    	}
    	
    }
   
}