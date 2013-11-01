<?php
class Escravo_SessoesController extends Zend_Controller_Action {

    public function init() {
        if(Zend_Auth::getInstance()->hasIdentity()
        && Zend_Auth::getInstance()->getIdentity()->user_id == 1
        && Zend_Auth::getInstance()->getIdentity()->user_tipo_id == 3){
        }else{
            $this->_redirect('/');
        }
        $this->view->contador = 0;
    }
    
    // AÇÃO //

    public function indexAction() {
        
    }

}