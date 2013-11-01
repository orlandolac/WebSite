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
class Default_UserController extends Alex_Controller {

    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/');
        }
        
        parent::init();
        $this->local[] = '<a href="/user">' . ucfirst(Zend_Auth::getInstance()->getIdentity()->user_apelido) . '</a>';
        $this->view->obj = Zend_Registry::get('db')->fetchRow('SELECT * FROM dfl_user WHERE user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
    }

    public function indexAction() {
        $this->_redirect('/user/noticias');
    }

    public function noticiasAction() {
        $this->local[] = '<a href="/user/noticias">Notícias</a>';
        $t = new Default_Model_Tabela_Noticia();
        $this->view->Lista = $t->buscar(array_merge($this->_request->getParams(), array('usuario' => Zend_Auth::getInstance()->getIdentity()->user_id)));
    }

    public function projetosAction() {
        $this->local[] = '<a href="/user/projetos">Projetos</a>';
        $t = new Default_Model_Tabela_Projeto();
        $this->view->Lista = $t->buscar(array_merge($this->_request->getParams(), array('usuario' => Zend_Auth::getInstance()->getIdentity()->user_id)));
    }

    public function partidosAction() {
        $this->local[] = '<a href="/user/partidos">Partidos</a>';
    }

    public function politicosAction() {
        $this->local[] = '<a href="/user/politicos">Políticos</a>';
    }
    
    //----------------------------------------------------------------------------------------------

    public function preferenciasAction() {
        if($this->getRequest()->isPost()){
            $post = $this->getRequest()->getPost();
            if(isset($post['secao'])){
                if($post['secao'] == 'localizacao'){
                    $this->salvarLocalizacao($post);
                }elseif($post['secao'] == 'temas'){
                    $this->salvarTemas($post);
                }elseif($post['secao'] == 'projetos'){
                    $this->salvarProjetos($post);
                }elseif($post['secao'] == 'noticias'){
                    $this->salvarNoticias($post);
                }
            }
            $this->_redirect('/user/preferencias');
        }
        
        $this->local[] = '<a href="/user/preferencias">Preferências</a>';
        $db = Zend_Registry::get('db');
        
        $regiao = $db->fetchRow('
            SELECT E.*, M.* 
            FROM dfl_user U, pu_municipio M, pu_estado E
            WHERE U.user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id . '
               && M.municipio_id=U.user_municipio_id
               && M.municipio_estado_id=E.estado_id
        ');
        if($regiao){
            $this->view->regiao = $regiao;
        }else{
            $this->view->regiao = 0;
        }
        
        $temas = $db->fetchAll('SELECT tema_id FROM dfl_usuario_x_temas WHERE user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        $this->view->temas = array();
        foreach ($temas as $value) {
            $this->view->temas[$value['tema_id']] = '';
        }
        
        $apreciacoes = $db->fetchAll('SELECT apreciacao_id FROM dfl_usuario_x_apreciacoes WHERE user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        $this->view->apreciacoes = array();
        foreach ($apreciacoes as $value) {
            $this->view->apreciacoes[$value['apreciacao_id']] = '';
        }
        
        $regimes = $db->fetchAll('SELECT regime_id FROM dfl_usuario_x_regimes WHERE user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        $this->view->regimes = array();
        foreach ($regimes as $value) {
            $this->view->regimes[$value['regime_id']] = '';
        }

        $situacoes = $db->fetchAll('SELECT situacao_id FROM dfl_usuario_x_situacoes WHERE user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        $this->view->situacoes = array();
        foreach ($situacoes as $value) {
            $this->view->situacoes[$value['situacao_id']] = '';
        }
        
        $tipos = $db->fetchAll('SELECT tplei_id FROM dfl_usuario_x_tipo_lei WHERE user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        $this->view->tipos = array();
        foreach ($tipos as $value) {
            $this->view->tipos[$value['tplei_id']] = '';
        }
        
        $veiculos = $db->fetchAll('SELECT veiculo_id FROM dfl_usuario_x_veiculos WHERE user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        $this->view->veiculos = array();
        foreach ($veiculos as $value) {
            $this->view->veiculos[$value['veiculo_id']] = '';
        }
        
    }

    public function configurarDadosAction() {
        $this->dialogo->add('Alerta', 'A alteração dos dados está temporáriamente indisponível.');
        $t = new Default_Model_Tabela_Usuario();
        $t->detalhar(Zend_Auth::getInstance()->getIdentity()->user_id);
        if($this->_request->isPost()){
            $t->setCps($this->_request->getParams());
            $t->salvar();
            $this->_redirect('/user/configurar-dados');
        }
        $this->local[] = '<a href="/user/configurar-dados">Configurar Dados</a>';
        $this->view->obj = $t->getCps();
    }

    public function configurarSenhaAction() {
        if($this->_request->isPost()){
            $t = new Default_Model_Tabela_Usuario();
            $t->detalhar(Zend_Auth::getInstance()->getIdentity()->user_id);
            $t->alterarSenha($this->_request->getParams());
            $this->_redirect('/user/configurar-senha');
        }
        $this->local[] = '<a href="/user/configurar-senha">Configurar Senha</a>';
    }

    public function fecharContaAction() {
        if($this->_request->isPost()){
            $t = new Default_Model_Tabela_Usuario();
            $t->detalhar(Zend_Auth::getInstance()->getIdentity()->user_id);
            if($t->fecharConta($this->_request->getParams())){
                $this->_redirect('/area-restrita/sair');
            }else{
                $this->_redirect('/user/fechar-conta');
            }
        }
        $this->local[] = '<a href="/user/fechar-conta">Fechar conta</a>';
    }
    
    // AJAX //
    
    public function ajaxPaginacaoNoticiasAction(){
        $this->_helper->layout->disableLayout();
        if($this->getRequest()->isPost()){
            $data = $this->_request->getParams();
            if(isset($data['controller']) && isset($data['action'])){
                if($data['controller'] == 'user' && $data['action'] == 'ajax-paginacao-noticias'){
                    $data = $this->getRequest()->getPost();
                    if(isset($data['pagina']) && is_numeric($data['pagina']) && $data['pagina'] % 1 == 0){
                        $t = new Default_Model_Tabela_Noticia();
                        $this->view->Lista = $t->buscar(array_merge($data, array('usuario' => Zend_Auth::getInstance()->getIdentity()->user_id)));
                    }
                }
            }
        }
    }
    
    // MÉTODOS //
    
    private function salvarLocalizacao($post) {
        if(isset($post['estado']) && $post['estado'] > 0 && isset($post['municipio']) && $post['municipio'] > 0){
            $db = Zend_Registry::get('db');
            $res = $db->fetchRow('SELECT * FROM pu_estado E, pu_municipio M WHERE E.estado_id=' . $db->quoteInto('?', $post['estado']) . ' && M.municipio_id=' . $db->quoteInto('?', $post['municipio']) . ' && E.estado_id=M.municipio_estado_id');
            if($res){
                $db->update('dfl_user', array(
                   'user_municipio_id' => $res['municipio_id']
                ), 'user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
                
                $this->localidade->estado_id        = $res['estado_id'];
                $this->localidade->estado_sigla     = $res['estado_sigla'];
                $this->localidade->estado_nome      = $res['estado_nome'];
                $this->localidade->municipio_id     = $res['municipio_id'];
                $this->localidade->municipio_nome   = $res['municipio_nome'];
                
                $this->dialogo->add('Ok', 'Suas preferências para a LOCALIZAÇÃO foram salvas com sucesso!');
            }else{
                $this->dialogo->add('Alerta', 'Não foi possível encontrar a região solicitada.');
            }
        }else{
            $this->dialogo->add('Erro', 'Requisição inválida!');
        }
    }
    
    private function salvarTemas($post) {
        if(isset($post['temas']) && is_array($post['temas']) && count($post['temas']) < 6 && count($post['temas']) > 2){
            $db = Zend_Registry::get('db');
            $db->delete('dfl_usuario_x_temas', 'user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
            foreach ($post['temas'] as $value) {
                $db->insert('dfl_usuario_x_temas', array(
                    'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
                    'tema_id' => $value
                ));
                $c++;
            }
            
            $res = $db->fetchAll('SELECT T.tema_id, UT.user_id, count(*) as QTD FROM pro_tema T LEFT JOIN dfl_usuario_x_temas UT ON T.tema_id=UT.tema_id GROUP BY T.tema_id ORDER BY QTD DESC');
            $max = $res[0]['QTD'];
            
            foreach ($res as $value) {
                $peso = (($value['user_id'] > 0)?($value['QTD']/$max):(0.01));
                $db->update('pro_tema', array(
                    'tema_peso' => (($peso > 0.01)?($peso):(0.01))
                ), 'tema_id=' . $value['tema_id']);
            }
            
            $this->dialogo->add('Ok', 'Suas preferências para os TEMAS foram salvas com sucesso!');
        }else{
            $this->dialogo->add('Alerta', 'Você precisa escolher de TRÊS a CINCO temas mais importantes.');
        }
    }
    
    private function salvarProjetos($post) {
        $db = Zend_Registry::get('db');
        $db->delete('dfl_usuario_x_apreciacoes', 'user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        $db->delete('dfl_usuario_x_regimes', 'user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        $db->delete('dfl_usuario_x_tipo_lei', 'user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        $db->delete('dfl_usuario_x_situacoes', 'user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        
        if(isset($post['apreciacoes']) && is_array($post['apreciacoes'])){
            foreach ($post['apreciacoes'] as $value) {
                $db->insert('dfl_usuario_x_apreciacoes', array(
                    'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
                    'apreciacao_id' => $value
                ));
            }
        }
        
        if(isset($post['regimes']) && is_array($post['regimes'])){
            foreach ($post['regimes'] as $value) {
                $db->insert('dfl_usuario_x_regimes', array(
                    'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
                    'regime_id' => $value
                ));
            }
        }
        
        if(isset($post['tipos']) && is_array($post['tipos'])){
            foreach ($post['tipos'] as $value) {
                $db->insert('dfl_usuario_x_tipo_lei', array(
                    'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
                    'tplei_id' => $value
                ));
            }
        }
        
        if(isset($post['situacoes']) && is_array($post['situacoes'])){
            foreach ($post['situacoes'] as $value) {
                $db->insert('dfl_usuario_x_situacoes', array(
                    'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
                    'situacao_id' => $value
                ));
            }
        }
        
        $this->dialogo->add('Ok', 'Suas preferências para os PROJETOS foram salvas com sucesso!');
    }
    
    private function salvarNoticias($post) {
        $db = Zend_Registry::get('db');
        $db->delete('dfl_usuario_x_veiculos', 'user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
        if(isset($post['veiculos']) && is_array($post['veiculos'])){
            foreach ($post['veiculos'] as $value) {
                $db->insert('dfl_usuario_x_veiculos', array(
                    'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
                    'veiculo_id' => $value
                ));
            }
        }
        $this->dialogo->add('Ok', 'Suas preferências para as NOTÍCIAS foram salvas com sucesso!');
    }
    
}