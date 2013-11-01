<?php

class Alex_Controller extends Zend_Controller_Action {

    protected $local = array();
    protected $dialogo = null;
    protected $localidade = null;

    // AÇÕES //
    
    public function indexAction() {

    }

    // MÉTODOS //
    
    public function init() {
        $sys = new Zend_Session_Namespace('sistem');
        $this->dialogo = $sys->dialogo;
        $this->localidade = $sys->localidade;
        $this->local[] = '<a href="/">Início</a>';
    }

    public function postDispatch() {
        $this->view->localidade = $this->localidade;
        $this->view->local = $this->local;
    }

}