<?php
/* Copyright 2013 de Alex de Oliveira.
 * Este arquivo é parte do programa OPovoUnido.com. O OPovoUnido.com é um software livre; você pode
 * redistribuí-lo e/ou modificá-lo dentro dos termos da [GNU General Public License OU GNU Affero
 * General Public License] como publicada pela Fundação do Software Livre (FSF); na versão 3 da
 * Licença. Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * licença para maiores detalhes. Você deve ter recebido uma cópia da [GNU General Public License
 * OU GNU Affero General Public License], sob o título "LICENCA.txt", junto com este programa, se
 * não, acesse http://www.gnu.org/licenses/
 */
class Default_EnquetesController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/enquetes">Enquetes</a>';
        $this->_redirect('/');
    }
    
    // AÇÕES //
        
    public function indexAction() {
        $t = new Default_Model_Tabela_Enquete();
        $get = $this->_request->getParams();
        if(count($get) == 3){
            $path = explode('/', $this->_request->getPathInfo());
            if(isset($path[2]) && $path[2] != ''){
                if(is_numeric($path[2]) && $path[2] % 1 == 0){
                    $obj = $t->detalhar($path[2]);
                }
                if($obj){
                    $this->view->obj = $obj->toArray();
                    $this->local[] = '<a href="/enquetes/' . $obj['enquete_id'] . '">' . $obj['enquete_titulo'] . '</a>';
                    $this->view->include = APPLICATION_PATH . '/modules/default/views/scripts/enquetes/detalhar.phtml';
                    return;
                }else{
                    if(!isset($get['palavra-chave'])){
                        $get['palavra-chave'] = $path[2];
                    }
                }
            }
        }
        if(isset($get['palavra-chave'])){
            $this->local[] = '<a href="/enquetes/palavra-chave/' . $get['palavra-chave'] . '">Buscar "' . $get['palavra-chave'] . '"</a>';
        }
        $this->view->Lista = $t->buscar($get);
        $this->view->include = APPLICATION_PATH . '/modules/default/views/scripts/enquetes/buscar.phtml';
    }
    
}