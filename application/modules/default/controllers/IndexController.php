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
class Default_IndexController extends Alex_Controller {

    public function indexAction() {
        
    }
    
    public function contatoAction() {
        $sys = new Zend_Session_Namespace('sistem');
        $t = new Default_Model_Tabela_Contato();
        if($this->getRequest()->isPost()){
            $t->setCps($this->getRequest()->getPost());
            if($t->salvar()){
                $t = new Default_Model_Tabela_Contato();
                $sys->tmp = null;
            }else{
                $sys->tmp = $this->getRequest()->getParams();
            }
            $this->_redirect('/contato');
        }else{
            if (isset($sys->tmp['module']) && $sys->tmp['module'] == $this->_request->getParam('module') && $sys->tmp['controller'] == $this->_request->getParam('controller') && $sys->tmp['action'] == $this->_request->getParam('action')) {
                $t->setCps($sys->tmp);
            } else {
                $sys->tmp = null;
            }
        }
        $this->view->obj = $t->getCps();
        $this->titulo = 'contato';
        $this->local[] = '<a href="/contato">Contato</a>';
    }
    
    public function mapaAction() {
        $this->local[] = '<a href="/mapa">Mapa</a>';
    }
    
    public function localizacaoAction() {
        $get = $this->getRequest()->getParams();
        if(isset($get['estado']) && isset($get['municipio']) && strlen($get['estado'])==2 && $get['municipio'] > 0){
            $get['estado'] = mb_strtoupper($get['estado']);
            $uf = Default_Model_Lista_EstadoSigla::get($get['estado']);
            if(isset($uf[0]) && $uf[0]>0 && $uf[0]!=6 && $uf[0]<29){
                $db = Zend_Registry::get('db');
                $res = $db->fetchRow('SELECT * FROM pu_municipio WHERE municipio_id=' . $db->quoteInto('?', $get['municipio']). ' && municipio_estado_id=' . $uf[0]);
                if($res){
                    $this->localidade->estado_id        = $res['municipio_estado_id'];
                    $this->localidade->estado_sigla     = $get['estado'];
                    $this->localidade->estado_nome      = $uf[1];
                    $this->localidade->municipio_id     = $res['municipio_id'];
                    $this->localidade->municipio_nome   = $res['municipio_nome'];
                    $this->dialogo->add('Ok', 'Localização alterada com sucesso!');
                }else{
                    $this->dialogo->add('Alerta', 'Não foi possivel configurar a localização solicitada.');
                }
                $this->_redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $this->dialogo->add('Infor', 'Para mudar a localização selecione o Estado e o Município desejado.');
        $this->_redirect('/brasil/territorio');
    }
    
    // AJAX //
    
    public function ajaxMensagemAction() {
        $this->_helper->layout->disableLayout();
        $get= $this->_request->getParams();
        if(isset($get['tipo']) && isset($get['mensagem'])){
            $msn = false;
            switch ($get['tipo']) {
                case 'Ok': ;
                case 'Erro': ;
                case 'Alerta': ;
                case 'Infor': $msn = true;
            }
            if($msn){
                $this->dialogo->add($get['tipo'], $get['mensagem']);
            }
        }
    }
 
    public function ajaxMunicipiosAction() {
        $this->_helper->layout->disableLayout();
        $get = $this->_request->getParams();
        $municipios = array();
        if(isset($get['estado']) && Default_Model_Lista_Estado::get($get['estado'])){
            $db = Zend_Registry::get('db');
            $municipios = $db->fetchAll('SELECT municipio_id, municipio_nome FROM pu_municipio WHERE municipio_estado_id=' . $db->quoteInto('?', $get['estado']));
        }
        $this->view->municipios = $municipios;
    }
    
    public function ajaxNadaAction() {
        $this->_helper->layout->disableLayout();
    }
    
}