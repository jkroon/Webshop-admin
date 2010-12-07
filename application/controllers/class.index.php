<?php

class index extends controller {

     public function beforeRun() {
         parent::beforeRun();
         parent::loadCustoms();
     }

     public function __index() {
        //pr($this -> model -> find('categories', 'all'));
     }

}