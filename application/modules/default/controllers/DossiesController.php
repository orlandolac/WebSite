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
class Default_DossiesController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/dossies">Dossiês</a>';
    }
    
    // AÇÕES //
    
    public function indexAction() {
        $t = new Default_Model_Tabela_Pessoa();
        $get = $this->_request->getParams();
        if(isset($get['palavra-chave']) && strlen($get['palavra-chave']) > 2){
            $this->local[] = '<a href="/dossies/palavra-chave/' . $get['palavra-chave'] . '">Buscar "' . $get['palavra-chave'] . '"</a>';
        }
        $this->view->Lista = $t->buscar($get);
        $this->view->include = APPLICATION_PATH . '/modules/default/views/scripts/dossies/buscar.phtml';
    }
        
    public function noticiasAction() {
        $this->detalhaSecao('Notícias', 'noticias');
        $t = new Default_Model_Tabela_Noticia();
        $this->view->Lista = $t->buscar(array_merge($this->view->get, array('pessoa' => $this->view->obj['pessoa_id'])));
    }
        
    public function projetosAction() {
        $this->detalhaSecao('Projetos', 'projetos');
        $t = new Default_Model_Tabela_Projeto();
        $this->view->Lista = $t->buscar(array_merge($this->view->get, array('pessoa' => $this->view->obj['pessoa_id'])));
    }

    public function patrimoniosAction() {
        $this->detalhaSecao('Patrimônios', 'patrimonios');
    }
    
    public function candidaturasAction() {
        $this->detalhaSecao('Candidaturas', 'candidaturas');
    }
    
    public function dadosAction() {
        $this->detalhaSecao('Dados', 'dados');
    }
        
    public function linksAction() {
        $this->detalhaSecao('Link\'s Relacionados', 'links');
    }
        
    // AJAX //
    
    public function ajaxFotoAction() {
        $this->_helper->layout->disableLayout();
        $get = $this->_request->getParams();
        if(isset($get['id']) && is_numeric($get['id']) && $get['id'] % 1 == 0){
            $db = Zend_Registry::get('db');
            $this->view->foto = $db->fetchRow('SELECT imagem_tipo, imagem_data FROM pu_pessoa_imagem WHERE pessoa_id=' . $db->quoteInto('?', $get['id']));
        }
    }
    
    public function ajaxPaginacaoAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'dossies' && $data['action'] == 'ajax-paginacao'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Pessoa();
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
                if($data['controller'] == 'dossies' && $data['action'] == 'ajax-paginacao-add'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Pessoa(array('max-itens' => 10));
                        if(isset($data['palavra-chave']) && is_numeric($data['palavra-chave']) && $data['palavra-chave'] % 1 == 0){
                            if(strlen($data['palavra-chave']) == 12){
                                $this->view->obj = $t->detalharTituloEleitor($data['palavra-chave']);
                            }elseif(strlen($data['palavra-chave']) == 11){
                                $this->view->obj = $t->detalharCpf($data['palavra-chave']);
                            }else{
                                $this->view->obj = $t->detalhar($data['palavra-chave']);
                            }
                        }else{
                            $this->view->Lista = $t->buscar($data);
                        }
                    }
                }
            }
        }
    }
    
    // MÉTODOS //
        
    public function detalhaSecao($secNome='', $secUrl='') {
        $t = new Default_Model_Tabela_Pessoa();
        $get = $this->_request->getParams();
        if(is_numeric($get['id']) && $get['id'] % 1 == 0){
            if(strlen($get['id']) == 12){
                $obj = $t->detalharTituloEleitor($get['id']);
            }elseif(strlen($get['id']) == 11){
                $obj = $t->detalharCpf($get['id']);
            }else{
                $obj = $t->detalhar($get['id']);
            }
        }else{
            $obj = $t->detalharUrl($get['id']);
        }
        
        if($obj){
            $this->view->get = $get;
            $this->view->obj = $obj->toArray();
            $this->local[] = '<a href="/dossies/' . $obj['pessoa_url'] . '">' . $obj['pessoa_nome'] . '</a>';
            if($secUrl != ''){
                $this->local[] = '<a href="/dossies/' . $obj['pessoa_url'] . '/' . $secUrl . '">' . $secNome . '</a>';
            }
        }else{
            $this->_redirect('/dossies/palavra-chave/' . $get['id']);
        }
    }
    
}