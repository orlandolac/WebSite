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
class Default_RankingController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/ranking">Ranking</a>';
    }
    
    // AÇÕES //
        
    public function indexAction() {
        $this->_redirect('/ranking/politicos');
    }
    
    public function politicosAction() {
        $this->local[] = '<a href="/ranking/politicos">Políticos</a>';
        $t = new Default_Model_Tabela_Ranking_Politicos();
        $this->view->Lista = $t->buscar($this->_request->getParams());
    }
    
    public function partidosAction() {
        $this->local[] = '<a href="/ranking/partidos">Partidos</a>';
        $t = new Default_Model_Tabela_Ranking_Partidos();
        $this->view->Lista = $t->buscar($this->_request->getParams());
    }
    
    // AJAX //
    
    public function ajaxPaginacaoPoliticosAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'ranking' && $data['action'] == 'ajax-paginacao-politicos'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Ranking_Politicos();
                        $this->view->Lista = $t->buscar($data);
                    }
                }
            }
        }
    }
    
    public function ajaxPaginacaoPartidosAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'ranking' && $data['action'] == 'ajax-paginacao-partidos'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Ranking_Partidos();
                        $this->view->Lista = $t->buscar($data);
                    }
                }
            }
        }
    }
    
}