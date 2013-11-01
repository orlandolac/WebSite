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
class Default_ProjetosController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/projetos">Projetos</a>';
    }
    
    // AÇÕES //
        
    public function indexAction() {
        $t = new Default_Model_Tabela_Projeto();
        $get = $this->_request->getParams();
        if(count($get) == 3){
            $path = explode('/', $this->_request->getPathInfo());
            if(isset($path[2]) && $path[2] != ''){
                if(is_numeric($path[2]) && $path[2] % 1 == 0){
                    $obj = $t->detalhar($path[2]);
                }else{
                    $obj = $t->detalharUrl($path[2]);
                }
                if($obj){
                    $this->view->obj = $obj;
                    $this->local[] = '<a href="/projetos/' . $obj['projeto_id'] . '">' . $obj['projeto_nome'] . '</a>';
                    $this->view->include = APPLICATION_PATH . '/modules/default/views/scripts/projetos/detalhar.phtml';
                    return;
                }else{
                    if(!isset($get['palavra-chave'])){
                        $get['palavra-chave'] = $path[2];
                    }
                }
            }
        }
        if(isset($get['palavra-chave'])){
            $this->local[] = '<a href="/projetos/palavra-chave/' . $get['palavra-chave'] . '">Buscar "' . $get['palavra-chave'] . '"</a>';
        }
        $this->view->Lista = $t->buscar($get);
        $this->view->include = APPLICATION_PATH . '/modules/default/views/scripts/projetos/buscar.phtml';
    }
    
    // AJAX //
    
    public function ajaxPaginacaoAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'projetos' && $data['action'] == 'ajax-paginacao'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Projeto();
                        $this->view->Lista = $t->buscar($data);
                    }
                }
            }
        }
    }
    
    public function ajaxProjetosVotarSimAction() {
        $this->_helper->layout->disableLayout();
        $this->view->obj = $this->ajaxProjetosVotar(1);
    }
    
    public function ajaxProjetosVotarNaoAction() {
        $this->_helper->layout->disableLayout();
        $this->view->obj = $this->ajaxProjetosVotar(2);
    }
    
    public function ajaxProjetosVotacaoAction(){
        $this->_helper->layout->disableLayout();
        $get = $this->getRequest()->getParams();
        if(isset($get['sigla']) && isset($get['numero']) && isset($get['ano'])){
            $lista = Camara_Proposicoes::obterVotacaoProposicao($get['sigla'], $get['numero'], $get['ano']);
            if(is_array($lista)){
                $this->view->votacoes = $lista['Votacoes']['Votacao'];
            }else{
                echo '<div style="padding:40px 20px 20px 20px;text-align:center"><i>VOTAÇÃO INDISPONÍVEL</i></div>';
            }
        }else{
            echo '<div style="padding:40px 20px 20px 20px;text-align:center"><i>VOTAÇÃO INDISPONÍVEL</i></div>';
        }
    }
    
    // MÉTODOS //
    
    private function ajaxProjetosVotar($voto) {
        $data = array('projeto_qtd_positivos' => 0, 'projeto_qtd_negativos' => 0);
        if($this->getRequest()->isPost()){
            $id = $this->getRequest()->getParam('id');
            if(isset($id) && is_numeric($id) && $id > 0 && $id % 1 == 0){
                if(Zend_Auth::getInstance()->hasIdentity()){
                    $db = Zend_Registry::get('db');
                    try {
                        $db->insert('pro_projeto_x_usuario', array(
                            'projeto_id'            => $id,
                            'user_id'               => Zend_Auth::getInstance()->getIdentity()->user_id,
                            'user_participacao_id'  => $voto
                        ));
                    } catch (Exception $exc) {
                        $db->update('pro_projeto_x_usuario', array(
                            'user_participacao_id'  => $voto
                        ), 'projeto_id=' . $db->quoteInto('?', $id) . ' && user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
                    }
                    $data = $db->fetchRow('SELECT projeto_id, projeto_qtd_positivos, projeto_qtd_negativos FROM pro_projeto WHERE projeto_id=' . $db->quoteInto('?', $id));
                    if(isset($data)){
                        Default_Model_Tabela_Projeto::calculaPontuacao($data['projeto_id']);
                    }
                }
            }
        }
        return $data;
    }
    
}