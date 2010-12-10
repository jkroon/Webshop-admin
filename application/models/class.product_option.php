<?php

class Product_option_model extends model {
	
	var $name = 'Product_option';
	
	var $hasMany = array('Product_suboptions');
	
}