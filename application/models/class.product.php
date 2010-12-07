<?php

class Product_model extends model {
	
	var $name = 'Product';

    var $validation = array('name' => 'notEmpty');
	
    var $hasMany = array('product_images', 'product_options');
    
    function beforeValidates($data) {
    	
    	// Eventuele productvariaties worden getest
    	if ($data['Product']['options'] == 'true') {
    		
    		if (!is_array($this -> validateErrors)) {
    			$this -> validateErrors = array();
    		}
    		
    		foreach($data['Product']['priceOptions'] as $key=>$value) {
    			
    				// De naam wordt gecontroleerd
    				if (empty($value['name'])) {
    					$this -> validateErrors['Product']['productOptions'][$key]['name'] = 'Dit is een verplicht veld';
    				}
    				
    				// De prijs wordt gecontroleerd
    				if (!preg_match("#^[0-9]{1,5}(\.|,){0,1}[0-9]{0,2}$#", $value['price'])) {
    					$this -> validateErrors['Product']['productOptions'][$key]['price'] = 'U heeft een ongeldig bedrag ingevuld';
    				}
    			
    		}
    		
    	} else {
    		
    		// De prijs wordt gecontroleerd
	    	if (!preg_match("#^[0-9]{1,5}(\.|,){0,1}[0-9]{0,2}$#", $data['Product']['price'])) {
	    		$this -> validateErrors['Product']['price'] = 'U heeft een ongeldig bedrag ingevuld';
	    	}
	    	
    	}
    	
    }
    
    
    function afterSave($data) {
    	
    	
    	// Indien er product opties aanwezig waren, dan worden deze verwijderd
    	if (isset($data['Product']['id'])) {
    		
    		// Alle eventuele product options worden verwijderd
    		$this -> Product_option -> delete(array('product_id' => $data['Product']['id']));
    		
    	}
    	
    	
    	// Er wordt bekeken of er product opties aanwezig zijn
    	if ($data['Product']['options'] == 'true') {
    		
    		// Het ID wordt bepaald
    		if (isset($data['Product']['id'])) {
    			$id = $data['Product']['id'];
    		} else {
    			$id = $this -> insert_id;
    		}
    		
    		// De product options worden opgeslagen in de database
    		foreach($data['Product']['priceOptions'] as $key=>$fields) {
    			
    			$array = array();
    			$array['Product_option']['name'] = $fields['name'];
    			$array['Product_option']['price'] = $fields['price'];
    			$array['Product_option']['product_id'] = $id;
    			
    			$this -> Product_option -> save($array);
    			
    		}
    		
    	}
    	
    }
   
}