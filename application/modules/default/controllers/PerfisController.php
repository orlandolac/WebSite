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
class Default_PerfisController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/perfis">Perfis</a>';
    }
    
    // AÇÕES //
        
    public function indexAction() {
        $t = new Default_Model_Tabela_Perfil();
        $get = $this->_request->getParams();
        if(isset($get['palavra-chave']) && strlen($get['palavra-chave']) > 2){
            $this->local[] = '<a href="/perfis/palavra-chave/' . $get['palavra-chave'] . '">Buscar "' . $get['palavra-chave'] . '"</a>';
        }
        $this->view->Lista = $t->buscar($get);
        $this->view->include = APPLICATION_PATH . '/modules/default/views/scripts/perfis/buscar.phtml';
    }
        
    public function noticiasAction() {
        $this->detalhaSecao('Notícias', 'noticias');
        $t = new Default_Model_Tabela_Noticia();
        $this->view->Lista = $t->buscar(array_merge($this->view->get, array('usuario' => $this->view->obj['user_id'])));
    }
        
    public function projetosAction() {
        $this->detalhaSecao('Projetos', 'projetos');
        $t = new Default_Model_Tabela_Projeto();
        $this->view->Lista = $t->buscar(array_merge($this->view->get, array('usuario' => $this->view->obj['user_id'])));
    }
     
    // AJAX //
    
    public function ajaxPaginacaoAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'perfis' && $data['action'] == 'ajax-paginacao'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Perfil();
                        $this->view->Lista = $t->buscar($data);
                    }
                }
            }
        }
    }
    
    // MÉTODOS //
        
    public function detalhaSecao($secNome='', $secUrl='') {
        $t = new Default_Model_Tabela_Perfil();
        $get = $this->_request->getParams();
        if(is_numeric($get['id']) && $get['id'] % 1 == 0){
            $obj = $t->detalhar($get['id']);
        }
        
        if($obj){
            $this->view->get = $get;
            $this->view->obj = $obj->toArray();
            $this->local[] = '<a href="/perfis/' . $obj['user_id'] . '">' . $obj['user_nome'] . '</a>';
            if($secUrl != ''){
                $this->local[] = '<a href="/perfis/' . $obj['user_id'] . '/' . $secUrl . '">' . $secNome . '</a>';
            }
        }else{
            $this->_redirect('/perfis/palavra-chave/' . $get['id']);
        }
    }
    
}