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
class Default_PartidosController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/partidos">Partidos</a>';
    }
    
    // AÇÕES //
        
    public function indexAction() {
        $t = new Default_Model_Tabela_Partido();
        $get = $this->_request->getParams();
        if(isset($get['palavra-chave']) && strlen($get['palavra-chave']) > 2){
            $this->local[] = '<a href="/partidos/palavra-chave/' . $get['palavra-chave'] . '">Buscar "' . $get['palavra-chave'] . '"</a>';
        }
        $this->view->Lista = $t->buscar($get);
        $this->view->include = APPLICATION_PATH . '/modules/default/views/scripts/partidos/buscar.phtml';
    }
        
    public function noticiasAction() {
        $this->detalhaSecao('Notícias', 'noticias');
        $t = new Default_Model_Tabela_Noticia();
        $this->view->Lista = $t->buscar(array_merge($this->view->get, array('partido' => $this->view->obj['partido_id'])));
    }
        
    public function projetosAction() {
        $this->detalhaSecao('Projetos', 'projetos');
        $t = new Default_Model_Tabela_Projeto();
        $this->view->Lista = $t->buscar(array_merge($this->view->get, array('partido' => $this->view->obj['partido_id'])));
    }

    public function patrimoniosAction() {
        $this->detalhaSecao('Patrimônios', 'patrimonios');
    }
    
    public function candidaturasAction() {
        $this->detalhaSecao('Candidaturas', 'candidaturas');
    }
     
    // AJAX //
    
    public function ajaxPaginacaoAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'partidos' && $data['action'] == 'ajax-paginacao'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Partido();
                        $this->view->Lista = $t->buscar($data);
                    }
                }
            }
        }
    }
    
    public function ajaxPaginacaoAddAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'partidos' && $data['action'] == 'ajax-paginacao-add'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Partido(array('max-itens' => 10));
                        $this->view->Lista = $t->buscar($data);
                    }
                }
            }
        }
    }
    
    // MÉTODOS //
        
    public function detalhaSecao($secNome='', $secUrl='') {
        $t = new Default_Model_Tabela_Partido();
        $get = $this->_request->getParams();
        if(is_numeric($get['id']) && $get['id'] % 1 == 0){
            $obj = $t->detalhar($get['id']);
        }else{
            $obj = $t->detalharUrl($get['id']);
        }
        
        if($obj){
            $this->view->get = $get;
            $this->view->obj = $obj->toArray();
            $this->local[] = '<a href="/partidos/' . $obj['partido_url'] . '">' . $obj['partido_nome'] . '</a>';
            if($secUrl != ''){
                $this->local[] = '<a href="/partidos/' . $obj['partido_url'] . '/' . $secUrl . '">' . $secNome . '</a>';
            }
        }else{
            $this->_redirect('/partidos/palavra-chave/' . $get['id']);
        }
    }
    
}