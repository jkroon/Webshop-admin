<?php

class products extends controller {
	
	public function beforeRun() {
		parent::beforeRun();
		parent::loadCustoms();
		
		// De CSS wordt geincluded
		$this -> template -> css(array('products', 'wysiwyg') );
		
		// De javascript wordt geincluded
		$this -> template -> javascript(array('products', 'jwysiwyg/jquery.wysiwyg'));
	}
	
	
	public function get($page='', $return=false) {
		
		// Het paginanummer wordt gecontroleerd
		if (empty($page)) {
			if (is_numeric($this -> id)) {
				$page = $this -> id;
			} else {
				return false;
			}
		}
		
		// Er wordt bekeken hoeveel producten er in totaal zijn
		$total = $this -> Product -> find('*', array(
			'onlyNumRows' => true
		));
		
		
		// Er wordt berekend of de pagina wel bestaat
		if ((($page - 1) * 10) > $total) {
			$result = array('success' => false);
		} else {
			
			// Het limiet wordt berekend
			if ($page == 1) {
				$limit = '0,10';
			} else {
				$limit = (($page - 1) * 10) . ',10';
			}
			
			
			// De producten worden opgehaald
			$result = $this -> Product -> find('id, name, price', array(
				'limit' => $limit
			));
			
			
			// Er wordt een afbeelding opgehaald voor ieder product
			$i = 0;
			foreach($result as $product) {
				$product = $product['Product'];
				
				$image = $this -> Product -> Product_image -> find('filename', array(
					'conditions' => array(
						'product_id' => $product['id']
					),
					'limit' => 1
				));
				
				
				// Indien er een image aanwezig is, wordt deze in de array geplaatst
				if ($this -> Product -> Product_image -> num_rows > 0) {
					$result[$i]['Product']['image'] = $image[0]['Product_image']['filename'];
				} else {
					$result[$i]['Product']['image'] = false;
				}
				
				// De counter wordt opgeteld
				++$i;
				
			}
			
			$result = array('success' => true, 'items' => $result);
			
		}
		
		// Het resultaat wordt geretouneerd
		if ($return) {
			return json_encode($result);
		} else {
			
			// De render functie wordt uitgeschakeld
			$this -> template -> noRender();
			
			echo json_encode($result);
		}
		
	}
	
	
	public function setview() {
		
		// De template render wordt uitgeschakeld
		$this -> template -> noRender();
		
		// De optie wordt geupdate
		if (url(3) == 'detail') {
			set_option('product_view', 'detail');
		} else {
			set_option('product_view', 'list');
		}

		// Er wordt data geretouneerd
		echo '1';		
	}
	
	
	public function __index() {
		
		// Er wordt bekeken hoeveel producten er in totaal zijn
		$num_rows = $this -> Product -> find('*', array(
			'onlyNumRows' => true
		));
		
		// De producten worden opgehaald
		$products = $this -> get(1, true);
		

		// Er wordt een nieuw block geopend in de template
		$this -> template -> newBlock('index');
		$this -> template -> assign(array(
			'products_count' => $num_rows,
			'products_pages' => ceil($num_rows / 10)
		) );
		
		
		// Er wordt bekeken welke view moet worden aangeroepen
		$view = read_option('product_view');
		
		if (!$view || $view == 'list') {
			$this -> template -> assign(array(
				'listView' => 'viewAct',
				'detailDiv' => 'display: none'
			));
		} else {
			$this -> template -> assign(array(
				'detailView' => 'viewAct',
				'listDiv' => 'display: none'
			));
		}
		
		
		// De json wordt in de template geplaatst
		$this -> template -> newBlock('index_json');
		$this -> template -> assign('json', $products);
		
	}
	
	
	public function add() {
		
		// Indien de POST aanwezig is, wordt deze gecontroleerd
		if ($this -> post) {			
			if ($this -> Product -> save($this -> post)) {
				$this -> setFlash('success', 'Het product is succesvol toegevoegd');				
				navigate(array('products', 'edit', $this -> Product -> insert_id));
			} else {
				$this -> setFlash('failure', 'U heeft een aantal velden incorrect ingevuld');
			}
		}
		
		// Het formulier wordt aangeroepen
		$this -> form('add');
		
	}
	
	
	public function edit() {
		
		// De data wordt opgeslagen indien de POST aanvraag aanwezig is
		if ($this -> post) {
			
			if ($this -> Product -> save($this -> post)) {
				$this -> setFlash('success', 'Het product is succesvol aangepast');
				navigate(array('products'));
			}
			
				
			// De prodct opties worden geladen
			if ($this -> post['Product']['options'] == 'true') {
				$product_options = true;
			} else {
				$product_options = false;
			}
			
		} else {		
			
			// De data wordt opgehaald
			$result = $this -> Product -> find('*', array(
				'conditions' => array(
					'id' => $this -> id
				),			
				'hasMany' => array(
					'product_options' => 'name, price'
				)
			));
			
			
			// De post data wordt aangemaakt						
			$this -> Product -> data = $result[0];
			$this -> post['Product'] = $result[0]['Product'];
			
			// Er wordt bekeken of er product opties aanwezig zijn
			if ($result[0]['Product']['options'] == 'true') {
				$product_options = true;
				$this -> post['Product']['priceOptions'] = $result[0]['product_options'];
			} else {
				$product_options = false;
			}


		}
		
		// Het formulier wordt aangeroepen
		$this -> form('edit', $product_options);
				
	}
	
	
	public function delete() {
		
		// De render wordt uigeschakeld
		$this -> template -> noRender();
		
		$id = $this -> id;
		
		if (is_numeric($id)) {
			if ($this -> Product -> delete($id)) {
				$return = array('success' => true);
			} else {
				$return = array('success' => false);
			}
		} else {
			$return = array('success' => false);
		}
		
		echo json_encode($return);
		
	}
	
	
	public function upload() {
		
		// Het renderen van een layout wordt uitgeschakeld
		$this -> template -> noRender();
				
		
		// De afbeelding wordt geüpload
		$image = $this -> loadPlugin('imageHandler');
		$image -> set($this -> post['Product_image']['file']);
		$image -> minWidth(150);
		$image -> minHeight(150);
		$image -> maxFileSize(600);
		$image -> thumbnails(array('50x50', '100x100', '150x150'));
		
		if ($image -> save(__DATA__ . 'public/uploads/')) {
			
			// De locatie van de afbeelding wordt opgeslagen
			$this -> post['Product_image']['filename'] = $image -> getFileName();
			
			// De afbeelding wordt in de database opgeslagen
			$this -> Product -> Product_image -> save($this -> post);
			
			// De JSON array wordt klaar gemaakt om te returnen
			$return = array('success' => true,
							'image' => $image -> getFileName(),
							'image_id' => $this -> Product -> Product_image -> insert_id);
			
		} else {
			$return = array('success' => false, 
							'error' => $image -> getError()
			);
		}
		
		// De status wordt middels JSON teruggestuurd
		echo json_encode($return);
				
	}
	
	
	public function getimages() {
		
		// Het renderen van de template wordt uitgeschakeld
		$this -> template -> noRender();
		
		$id = $this -> id;
		
		$images = $this -> Product -> Product_image -> find('id, filename', array('conditions' => array(
				  	'product_id' => $id
				  )));
				  
		// De afbeeldingen worden in een array geplaatst
		$json = array();
		$i = 0;
		
		foreach($images as $each) {
			foreach($each as $table=>$array) {
				
				$json[$i]['id'] = $array['id'];
				$json[$i]['filename'] = $array['filename'];
				
				++$i;
			}
		}
		
		echo json_encode($json);
		
	}
	
	
	public function deleteimage() {
		
		// De automatische render functie wordt uitgeschakeld
		$this -> template -> noRender();
		
		$id = $this -> id;
		
		if ($this -> Product -> Product_image -> delete($id)) {
			echo json_encode(array('success' => true));
		} else {
			echo json_encode(array('success' => false));
		}
		
	}
	
	
	public function form($action, $product_options=false) {	
		
		
		// De javascript bestanden voor uploadify worden ingeladen
		$this -> template -> javascript(array('swfobject', 'jquery.uploadify.v2.1.4.min'));

		
		// Er wordt een block in de template geopend
		$this -> template -> newBlock('add');
		
		
		// Alle subcategorieen worden opgehaald
		$categories = $this -> Category -> find('id, name', array(
			'conditions' => array(
				'parent_id IS NOT NULL' => false
			)
		));
		
		// Het bovenstaande resultaat wordt omgezet naar een lijst
		$options = make_list($categories);
		
		// De plugin wordt geladen
		$this -> loadPlugin('form');
		
		// Het formulier wordt geinstantieerd en er wordt een model aan toegewezen
		$form = new formPlugin();
		$form -> bindModel($this -> Product);
		
		// De selectie box voor de categorie
		$form -> select('category_id', array('options' => $options, 'label' => 'Categorie'), 'Product');		
		$this -> template -> assign('inputPrdSelect', $form -> getOutput());
				
		// De productnaam
		$form -> text('name', array('label' => 'productnaam'), 'Product');
		$this -> template -> assign('inputPrdName', $form -> getOutput()); 
		
	
		// De prijs van het product
		$form -> text('price', array('label' => 'Prijs'), 'Product');
		$this -> template -> assign('inputPrdPrice', $form -> getOutput());
		
		
		// De productomschrijving
		$form -> textarea('description', array('style' => 'width: 500px; height: 200px', 'id' => 'editor'), 'Product');
		$this -> template -> assign('inputDescription', $form -> getOutput());
		
		
		// het hidden input veld wordt verteld of er gebruik wordt gemaakt van product opties
		$this -> template -> assign('inputOptionsHidden', ($product_options ? 'true' : 'false'));
		
		
		// De product opties worden ingeladen
		if ($product_options) {			

			// Er wordt geswitched naar het juiste block
			$this -> template -> assign(array('useOptionsPrice' => 'display: block',
											  'useFixedPrice' => 'display: none'));
			
			$this -> template -> assign(array('fixedPriceActive' => '',
											   'optionsPriceActive' => 'viewAct'));
			
			// Alle product opties worden ingeladen
			foreach($this -> post['Product']['priceOptions'] as $key=>$array) {
				
				$array['id'] = $key;
				$array['checked'] = (isset($array['custom']) ? 'checked' : '');
				
				// Er wordt bekeken of er fouten aanwezig zijn in de product opties					
				if (isset($this -> Product -> validateErrors['Product']['productOptions'][$key])) {
					
					$errors = $this -> Product -> validateErrors['Product']['productOptions'][$key];
					
				} else {
					
					$errors = false;
				}
				
				// Een nieuw block in de template wordt geopend
				$this -> template -> newBlock('productPriceOption');
				$this -> template -> assign('id', $key);
				
					
				// Het block voor de productnaam wordt geopend
				if (isset($errors['name'])) {
					$this -> template -> newBlock('productPriceOptionError');
					$this -> template -> assign($array);
				} else {
					$this -> template -> newBlock('productPriceOptionNormal');
					$this -> template -> assign($array);
				}
				
				
				// Het block voor de prijs wordt geopend
				if (isset($errors['price'])) {
					$this -> template -> newBlock('productPriceOptionPrcError');
					$this -> template -> assign($array);
				} else {
					$this -> template -> newBlock('productPriceOptionPrcNormal');
					$this -> template -> assign($array);
				}
					
				
				
			}
			
		} else {
			
			// Er wordt een enkel block geopend in de template
			$this -> template -> assign(array('useOptionsPrice' => 'display: none',
											  'useFixedPrice' => 'display: block'));			
			
			$this -> template -> assign(array('fixedPriceActive' => 'viewAct',
											   'optionsPriceActive' => ''));
			
			$this -> template -> newBlock('productPriceOptionClear');
			$this -> template -> assign('id', 1);
			
		}
		
		
		// Indien er een edit is worden bepaalde functies uitgevoerd
		if ($action == 'edit') {
			
			// Het ID wordt meegegeven
			$this -> template -> newBlock('id_block');
			$this -> template -> assign('id', $this -> id);
			
			// Een afbeelding block wordt meegegeven
			$this -> template -> newBlock('upload_image');
			$this -> template -> assign('id', $this -> id);
			$this -> template -> assign('session_id', session_id());
			
			// Alle afbeeldingen worden ingeladen
			$images = $this -> Product -> Product_image -> find('id, filename', array('conditions' => array(
				'product_id' => $this -> id
			)));
			
			foreach($images as $image) {
				$image = $image['Product_image'];
				
				// Het veld ID wordt hernoemd naar image_id om problemen met het product ID te voorkomen
				$image['image_id'] = $image['id'];
				unset($image['id']);
				
				$this -> template -> newBlock('uploaded_image');
				$this -> template -> assign($image);
			}
		}
		
		
		// De submit button wordt geopend
		$this -> template -> newBlock('formSubmit');
		$this -> template -> assign('button', ($action == 'edit' ? 'Opslaan' : 'Ga naar stap 2'));
		
	}
	
}