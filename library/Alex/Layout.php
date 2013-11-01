<?php

class Alex_Layout extends Zend_Controller_Plugin_Abstract {

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $layout = Zend_Layout::startMvc();
        $layout->setLayout('Layout');
        $tema = APPLICATION_PATH . '/modules/' . $request->getModuleName() . '/layouts';
        $layout->setLayoutPath($tema);
    }

}