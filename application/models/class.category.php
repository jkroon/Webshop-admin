<?php

class Category_model extends model {

	var $name = 'Category';

     var $validation = array('name' => 'notEmpty');
	
}