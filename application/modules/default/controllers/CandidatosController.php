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
class Default_CandidatosController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/candidatos">Candidatos</a>';
    }
    
    public function indexAction() {
        $this->_redirect('/candidatos/presidentes');
    }
    
    public function presidentesAction() {
        $this->buscarCandidaturas(1, 'Presidentes', '/candidatos/presidentes');
    }
    
    public function deputadosFederaisAction() {
        $this->buscarCandidaturas(6, 'Deputados Federais', '/candidatos/deputados-federais');
    }

    public function senadoresAction() {
        $this->buscarCandidaturas(5, 'Senadores', '/candidatos/senadores');
    }

    public function governadoresAction() {
        $this->buscarCandidaturas(3, 'Governadores', '/candidatos/governadores');
    }

    public function deputadosDistritaisAction() {
        if($this->localidade->estado_id != 8){
            $this->_redirect('/candidatos/deputados-estaduais');
        }
        $this->buscarCandidaturas(8, 'Deputados Distritais', '/candidatos/deputados-distritais');
    }

    public function deputadosEstaduaisAction() {
        if($this->localidade->estado_id == 8){
            $this->_redirect('/candidatos/deputados-distritais');
        }
        $this->buscarCandidaturas(7, 'Deputados Estaduais', '/candidatos/deputados-estaduais');
    }

    public function prefeitosAction() {
        if($this->localidade->estado_id == 8){
            $this->_redirect('/candidatos/presidentes');
        }
        $this->buscarCandidaturas(11, 'Prefeitos', '/candidatos/prefeitos');
    }

    public function vereadoresAction() {
        if($this->localidade->estado_id == 8){
            $this->_redirect('/candidatos/presidentes');
        }
        $this->buscarCandidaturas(13, 'Vereadores', '/candidatos/vereadores');
    }
    
    // AJAX //
    
    
    public function ajaxPaginacaoAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'candidatos' && $data['action'] == 'ajax-paginacao'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['cargo']) && Default_Model_Lista_Cargo::get($data['cargo'])){
                        if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                            $t = new Default_Model_Tabela_Candidatura();
                            $this->view->Lista = $t->buscar($data, $data['cargo']);
                        }
                    }
                }
            }
        }
    }
    
    // MÉTODOS //
    
    protected function buscarCandidaturas($id, $nome, $caminho) {
        $get = $this->_request->getParams();
        if(!(isset($get['eleicao']) && Default_Model_Lista_Eleicao::get($get['eleicao']))){
            $this->_redirect($caminho . '/' . (($id > 10)?(ELEICAO_MUNICIPAL):(ELEICAO_MAJORITAIA)));
        }
        $this->local[] = '<a href="' . $caminho . '">' . $nome . '</a>';
        if(isset($get['palavra-chave'])){
            $this->local[] = '<a href="' . $caminho . '/' . $get['palavra-chave'] . '">Buscar "' . $get['palavra-chave'] . '"</a>';
        }
        $t = new Default_Model_Tabela_Candidatura();
        $this->view->Lista = $t->buscar($get, $id);
        $this->view->cargo_id = $id;
        $this->view->cargo_nome = $nome;
        $this->view->cargo_caminho = $caminho;
    }
    
}