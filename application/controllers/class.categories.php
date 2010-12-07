<?php

class categories extends controller {

     public function beforeRun() {
          parent::beforeRun();
          $this -> loadCustoms();  
		  
		  $this -> niks = 'niks';
     }

     public function __index() {
          
          // De CSS wordt ingeladen
          $this -> template -> css('categories');

          // Een block in de template wordt geöpend
          $this -> template -> newBlock('overview');
          
          // De categorieen worden opgehaald
          $categories = $this -> getcategories(1, true);
          
          // Het aantal categorieen wordt opgehaald
          $count = $this -> Category -> find('*', array(
          	'conditions' => array(
          		'parent_id IS NOT NULL' => false
          	),
          	'onlyNumRows' => true
          ));
          
          $this -> template -> assign('category_count', $count);
          $this -> template -> assign('category_pageTotal', ceil($count / 5));
          
          $this -> template -> newBlock('index_json');
          $this -> template -> assign('json', $categories);

     }
     
     
     public function getcategories($page='', $return=false) {
     	
     	// De template render wordt uitgeschakeld
     	if (!$return) {
     		$this -> template -> noRender();
     	}
     	
     	// Het pagina nummer wordt opgehaald
     	if (empty($page)) {
     		$page = ($this -> id ? $this -> id : false);
     	}
     	
     	if (!is_numeric($page)) {
     		if (!$return) {
     			echo json_encode(array('success' => false));
     		}
     		
     		
     		return false;
     	}

     	// Er wordt bekeken hoeveel producten er in totaal zijn
		$total = $this -> Category -> find('*', array(
			'onlyNumRows' => true,
			'conditions' => array(
				'parent_id IS NOT NULL' => false
			)
		));
		
		// Er wordt berekend of de pagina wel bestaat
		if ((($page - 1) * 5) >= $total) {
			$result = array('success' => false);
		} else {
			
			// Het limiet wordt berekend
			if ($page == 1) {
				$limit = '0,5';
			} else {
				$limit = (($page - 1) * 5) . ',5';
			}
			
			
			// De categorieen worden opgehaald
			$categories = $this -> Category -> find('id, name', array(
				'conditions' => array(
					'parent_id IS NOT NULL' => false
				),
				'limit' => $limit
			));
			
			// Het resultaat wordt aangemaakt
			$result = array('success' => true, 'items' => $categories);
			
		}

		if ($return) {
			return json_encode($result);
		} else {
			echo json_encode($result);
		}

     }


     public function add() {

          // De data wordt opgeslagen indien deze door de validatie is gekomen.
          if ($this -> post) {
               if ($this -> Category -> save($this -> post)) {
                    $this -> setFlash('success', 'De categorie is succesvol opgeslagen');
                    navigate(array('categories'));
               } else {

               }
          }

          // Het formulier wordt ingeladen
          $this -> form('toevoegen');
     }


     public function edit() {

          // De data wordt opgeslagen indien deze door de validatie is gekomen.
          if ($this -> post) {
               if ($this -> Category -> save($this -> post)) {
                    $this -> setFlash('success', 'De categorie is succesvol opgeslagen');
                    navigate(array('categories'));
               } else {

               }
          } else {

               // De waardes worden opgehaald
               $data = $this -> Category -> findById($this -> id);
               $this -> Category -> data = $data;
          }

          // Het formulier wordt ingeladen
          $this -> form('bewerken', true);
     }
     
     
     public function delete() {
     	
     	if ($this -> Category -> delete($this -> id)) {
     		$this -> setFlash('success', 'De categorie is succesvol verwijderd');
     	}
     	
     	navigate(array('categories'));
     	
     }


     public function form($action, $edit=false) {

          // De CSS wordt geïncluded
          $this -> template -> css('form');

          // De plugin voor het formulier wordt opgehaald
          $form = $this -> loadPlugin('form');
          $form -> bindModel($this -> Category);

          // Het formulier wordt aangemaakt
          $form -> create('Categories');

          // De velden worden aangemaakt
          $form -> text('name', array('label' => 'Naam', 'maxlength' => '20'));
          $form -> checkbox('active', array('label' => 'Actief', 'defaultChecked' => 'true'));

          // Indien noodzakelijk wordt het ID voor bewerken aangemaakt
          if ($edit) {
               $form -> hidden('id');
          }

          // Het formulier wordt afgesloten
          $form -> end('Verzenden');

          // De output wordt gegenereerd
          $output = $form -> getOutput();

          // De output wordt in de template geplaatst
          $this -> template -> newBlock('form');
          $this -> template -> assign('form', $output);
          $this -> template -> assign('action', $action);
     }

}