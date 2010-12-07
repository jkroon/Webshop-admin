<?php

class Product_image_model extends model {
	
	var $name = 'Product_image';
	
	
    function beforeDelete($id) {

    	// De afbeeldingen wordt opgehaald
    	$image = $this -> findById($id);
    	
    	$filename = $image['Product_image']['filename'];
    	
    	// De afbeelding wordt verwijderd
    	unlink(__DATA__ . 'public'. DS .'uploads' . DS . $image['Product_image']['filename']);
    	
    	
    	// Eventuele thumbnails worden verwijderd
    	for($i=0;$i<5;++$i) {
    		$dir = __DATA__ . 'public' . DS . 'uploads' . DS . $i . '__' . $filename;
    		
    		if (file_exists($dir)) {
    			unlink($dir);
    		}
    	}
    }
	
}