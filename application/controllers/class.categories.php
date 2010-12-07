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

     }


     public function toevoegen() {

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


     public function bewerken() {

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