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
class Default_NoticiasController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/noticias">Notícias</a>';
    }
    
    // AÇÕES //
        
    public function indexAction() {
        $t = new Default_Model_Tabela_Noticia();
        $get = $this->_request->getParams();
        if(count($get) == 3){
            $path = explode('/', $this->_request->getPathInfo());
            if(isset($path[2]) && $path[2] != ''){
                if(is_numeric($path[2]) && $path[2] % 1 == 0){
                    $obj = $t->detalhar($path[2]);
                }
                if($obj){
                    $this->view->obj = $obj;
                    $this->view->obj['pessoas'] = Zend_Registry::get('db')->fetchAll('SELECT * FROM not_noticia_x_pessoa N, pu_pessoa P WHERE N.noticia_id=' . $obj['noticia_id'] . ' && N.pessoa_id=P.pessoa_id');
                    $this->view->obj['partidos'] = Zend_Registry::get('db')->fetchAll('SELECT * FROM not_noticia_x_partido N, pu_partido P WHERE N.noticia_id=' . $obj['noticia_id'] . ' && N.partido_id=P.partido_id');
                    $this->local[] = '<a href="/noticias/' . $obj['noticia_id'] . '">' . $obj['noticia_titulo'] . '</a>';
                    $this->view->include = APPLICATION_PATH . '/modules/default/views/scripts/noticias/detalhar.phtml';
                    return;
                }else{
                    if(!isset($get['palavra-chave'])){
                        $get['palavra-chave'] = $path[2];
                    }
                }
            }
        }
        if(isset($get['palavra-chave'])){
            $this->local[] = '<a href="/noticias/palavra-chave/' . $get['palavra-chave'] . '">Buscar "' . $get['palavra-chave'] . '"</a>';
        }
        
        $this->view->Lista = $t->buscar($get);
        $this->view->include = APPLICATION_PATH . '/modules/default/views/scripts/noticias/buscar.phtml';
    }
    
    // AJAX //
    
    public function ajaxPaginacaoAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'noticias' && $data['action'] == 'ajax-paginacao'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Noticia();
                        $this->view->Lista = $t->buscar($data);
                    }
                }
            }
        }
    }
    
    public function ajaxNoticiasAddAction() {
        $this->_helper->layout->disableLayout();
        $uri = $this->getRequest()->getParam('url');
        if(isset($uri)){
            $uri = explode('/', substr($uri, 1));
            if(count($uri) > 1 && $uri[1] != 'palavra-chave'){
                $db = Zend_Registry::get('db');
                $query = $db->quoteInto('_url=?', $uri[1]);
                if(is_numeric($uri[1]) && $uri[1] > 0 && $uri[1] % 1 == 0){
                    $query = $db->quoteInto('_id=?', $uri[1]);
                }
                if($uri[0] == 'dossies'){
                    $this->view->pessoa = $db->fetchRow('SELECT pessoa_id, pessoa_nome FROM pu_pessoa WHERE pessoa' . $query);
                }elseif($uri[0] == 'partidos'){
                    $this->view->partido = $db->fetchRow('SELECT partido_id, partido_nome FROM pu_partido WHERE partido' . $query);
                }
            }
        }
    }
    
    public function ajaxNoticiasAddLoadAction() {
        $this->_helper->layout->disableLayout();
        $this->view->data = 0;
        $url = $this->getRequest()->getParam('url');
        if(isset($url)){
            if(!(filter_var($url, FILTER_VALIDATE_URL) === FALSE)){
                $this->view->data = Default_Model_Link::getData($url);
            }
        }
    }
    
    public function ajaxNoticiasAddSalvarAction() {
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $t = new Default_Model_Tabela_Noticia();
            $t->cadastrar($this->getRequest()->getPost());
        }
    }
    
    public function ajaxNoticiasImportanciaAction() {
        $this->_helper->layout->disableLayout();
        if(Zend_Auth::getInstance()->hasIdentity()){
            $get = $this->_request->getParams();
            if(isset($get['id']) && is_numeric($get['id']) && $get['id'] % 1 == 0){
                if(isset($get['user_importancia_id']) && is_numeric($get['user_importancia_id']) && $get['user_importancia_id'] % 1 == 0 && $get['user_importancia_id'] > 0 && $get['user_importancia_id'] < 6){
                    $db = Zend_Registry::get('db');
                    try {
                        $db->insert('not_noticia_x_usuario', array(
                            'noticia_id' => $get['id'],
                            'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
                            'user_importancia_id' => $get['user_importancia_id']
                        ));
                    } catch (Exception $exc) {
                        $db->update('not_noticia_x_usuario', array('user_importancia_id' => $get['user_importancia_id']), 'noticia_id=' . $db->quoteInto('?', $get['id']) . ' && user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
                    }
                    $this->view->obj = $get;
                }
            }
        }
    }
    
    public function ajaxNoticiasLigarAction() {
        $this->_helper->layout->disableLayout();
        $get = $this->_request->getParams();
        if(isset($get['id']) && is_numeric($get['id']) && $get['id'] % 1 == 0){
            $db = Zend_Registry::get('db');
            $db->update('not_noticia', array(
                'noticia_qtd_ligacao' => new Zend_Db_Expr('noticia_qtd_ligacao+1')
            ), 'noticia_id=' . $db->quoteInto('?', $get['id']));
        }
    }
    
    public function ajaxNoticiasPartidoLigarAction() {
        $this->_helper->layout->disableLayout();
        $get = $this->_request->getParams();
        if(isset($get['id']) && is_numeric($get['id']) && $get['id'] % 1 == 0){
            if(isset($get['partido_id']) && is_numeric($get['partido_id']) && $get['partido_id'] % 1 == 0){
                $db = Zend_Registry::get('db');
                $db->update('not_noticia_x_partido', array(
                    'noticia_aux_qtd_ligacao' => new Zend_Db_Expr('noticia_aux_qtd_ligacao+1')
                ), 'partido_id=' . $db->quoteInto('?', $get['partido_id']) . ' && noticia_id=' . $db->quoteInto('?', $get['id']));
            }
        }
    }
    
    public function ajaxNoticiasPessoaLigarAction() {
        $this->_helper->layout->disableLayout();
        $get = $this->_request->getParams();
        if(isset($get['id']) && is_numeric($get['id']) && $get['id'] % 1 == 0){
            if(isset($get['pessoa_id']) && is_numeric($get['pessoa_id']) && $get['pessoa_id'] % 1 == 0){
                $db = Zend_Registry::get('db');
                $db->update('not_noticia_x_pessoa', array(
                    'noticia_aux_qtd_ligacao' => new Zend_Db_Expr('noticia_aux_qtd_ligacao+1')
                ), 'pessoa_id=' . $db->quoteInto('?', $get['pessoa_id']) . ' && noticia_id=' . $db->quoteInto('?', $get['id']));
            }
        }
    }
    
}