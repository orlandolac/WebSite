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
class Default_AreaRestritaController extends Alex_Controller {

    public function init() {
        parent::init();
        $action = $this->getRequest()->getParam('action');
        if($action != 'sair' && $action != 'entrar-facebook' && $action != 'entrar-google' && $action != 'entrar-twitter' && $action != 'entrar-linkedin'){
            if (Zend_Auth::getInstance()->hasIdentity()) {
                $this->dialogo->add('Infor', 'Você já está cadastrado e autenticado!');
                $this->_redirect('/');
            }
        }
        $this->local[] = '<a href="/area-restrita">Área restrita</a>';
    }

    public function cadastreSeAction() {
        $this->dialogo->add('Alerta', 'O cadastro tradicional está temporáriamente indisponível, utilize o <b>Facebook</b> ou <b>Google+</b>.');
        $sys = new Zend_Session_Namespace('sistem');
        $t = new Default_Model_Tabela_Usuario();
        if($this->getRequest()->isPost()){
            $get = $this->getRequest()->getPost();
            $parts = explode(' ', $get['user_nome']);
            foreach ($parts as $key => $value) {
                if(strlen($value) > 0){
                    $get['user_apelido'] = $value;
                    break;
                }
            }
            
            $t->setCps($get);
            if($t->salvar()){
                $t = new Default_Model_Tabela_Usuario();
                $sys->tmp = null;
                $this->_redirect('/area-restrita');
            }else{
                $sys->tmp = $this->getRequest()->getParams();
            }
            $this->_redirect('/area-restrita/cadastre-se');
        }else{
            if (isset($sys->tmp['module']) && $sys->tmp['module'] == $this->_request->getParam('module') && $sys->tmp['controller'] == $this->_request->getParam('controller') && $sys->tmp['action'] == $this->_request->getParam('action')) {
                $t->setCps($sys->tmp);
            } else {
                $sys->tmp = null;
            }
        }
        $this->titulo = 'cadastre-se';
        $this->local[] = '<a href="/area-restrita/cadastre-se">Cadastre-se</a>';
        $this->view->obj = $t->getCps();
    }

    public function confirmarCadastroAction(){
        $get = $this->_request->getParams();
        if(isset($get['solicitacao']) &&  strlen($get['solicitacao']) > 40){
            $parts = explode('-', $get['solicitacao']);
            $user_id = $parts[(count($parts)-1)];
            if($user_id > 0){
                $t = new Default_Model_Tabela_Usuario();
                if($t->detalhar($user_id)){
                    $t->confirmarCadastro();
                }else{
                    $this->dialogo->add('Alerta', 'A conta especificada não foi encontrada.');
                }
            }
        }elseif(isset($get['usuario']) && strlen($get['usuario']) == 40){
            $db = Zend_Registry::get('db');
            $res = $db->fetchAll('SELECT user_id, user_email FROM dfl_user WHERE user_status_id=0');
            if(count($res) > 0){
                foreach ($res as $obj) {
                    if($get['usuario'] == sha1($obj['user_email'])){
                        $db->update('dfl_user', array('user_status_id' => 1), 'user_id=' . $obj['user_id']);
                        $this->dialogo->add('Ok', 'O cadastro foi confirmado! Você já pode utilizar sua conta.');
                        $this->_redirect('/area-restrita');
                    }
                }
            }
            $this->dialogo->add('Alerta', 'Esta confirmação não é mais válida.');
        }
        $this->_redirect('/area-restrita');
    }

    public function esqueciSenhaAction() {
        $get = $this->_request->getParams();
        if($this->getRequest()->isPost() && isset($get['user_email'])){
            $t = new Default_Model_Tabela_Usuario();
            if($t->detalharEmail($get['user_email'])){
                $t->confirmarNovaSenha();
            }else{
                $this->dialogo->add('Alerta', 'Nenhuma conta foi encontrada com este e-mail.');
            }
            $this->_redirect('/area-restrita/esqueci-senha');
        }elseif(isset($get['usuario']) && strlen($get['usuario']) > 40){
            $parts = explode('-', $get['usuario']);
            $user_id = $parts[(count($parts)-1)];
            $t = new Default_Model_Tabela_Usuario();
            if($t->detalhar($user_id)){
                if($t->setNovaSenha($get['usuario'])){
                    $this->_redirect('/area-restrita');
                };
            }else{
                //$this->dialogo->add('Alerta', 'A conta especificada não foi encontrada.');
            }
            $this->_redirect('/area-restrita/esqueci-senha');
        }

        $this->titulo = 'esqueci senha';
        $this->local[] = '<a href="/area-restrita/esqueci-senha">Esqueci a senha</a>';
    }

    public function entrarAction() {
        if ($this->_request->isPost()) {
            if($this->login($this->getRequest()->getParams())){
                $this->_redirect('/');
            }else{
                $this->_redirect('/area-restrita');
            }
        }else{
            $historico = new Default_Model_Tabela_Historico('his_login');
            $historico->newLog(1);
        }
        $this->_redirect('/');
    }

    public function entrarFacebookAction(){
        require APPLICATION_PATH . '/../library/Facebook/facebook.php';
        $facebook = new Facebook(array(
            'appId'  => '484204754972513',
            'secret' => '2a2b335c0216801cb706c837533bba4c',
            'fileUpload' => true,
        ));
        
        $user_id = $facebook->getUser();
        if($user_id) {
            $stt = true;
            try {
                $data = $facebook->api('/me?fields=id,name,first_name,email,gender,location,picture','GET');
            } catch(FacebookApiException $e) {
                $stt = false;
            }
            
            if($stt){
                $db = Zend_Registry::get('db');
                $tmp = $db->fetchRow('SELECT * FROM dfl_user WHERE user_id_facebook=' . $db->quoteInto('?', $data['id']));
                if(isset($tmp) && $tmp['user_id'] > 0){
                    $db->update('dfl_user', array(
                        'user_apelido'  => $data['first_name'],
                        'user_nome'     => $data['name'],
                        'user_icone'    => $data['picture']['data']['url'],
                        'user_sexo_id'     => (($data['gender']=='male')?('1'):('2')),
                    ), 'user_id=' . $tmp['user_id']);
                    
                    if($this->login($tmp, 'Facebook')){
                        $this->_redirect('/');
                    }else{
                        //$this->_redirect('/area-restrita');
                    }
                }else{
                    $tmp = $db->fetchRow('SELECT * FROM dfl_user WHERE user_email=' . $db->quoteInto('?', $data['email']));
                    if(isset($tmp) && $tmp['user_id'] > 0){
                        if($tmp['user_id_facebook'] > 0){
                            $this->dialogo->add('Erro', 'Este e-mail já está sendo utilizado por outro usuário.');
                            $this->dialogo->add('Alerta', 'Se este e-mail lhe pertence entre em contato conosco para solucionarmos o problema.');
                        }else{
                            $db->update('dfl_user', array(
                                'user_apelido'      => $data['first_name'],
                                'user_nome'         => $data['name'],
                                'user_icone'        => $data['picture']['data']['url'],
                                'user_sexo_id'         => (($data['gender']=='male')?('1'):('2')),
                                'user_id_facebook'  => $data['id'],
                            ), 'user_id=' . $tmp['user_id']);
                            if($this->login($tmp, 'Facebook')){
                                $this->_redirect('/');
                            }else{
                                //$this->_redirect('/area-restrita');
                            }
                        }
                    }else{
                        $t = new Default_Model_Tabela_Usuario();
                        $senha = $t->getNovaSenha();
                        $user = array(
                            'user_status_id'       => '1',
                            'user_tipo_id'      => '1',
                            'user_email'        => $data['email'],
                            'user_senha'        => sha1($senha),
                            'user_apelido'      => $data['first_name'],
                            'user_nome'         => $data['name'],
                            'user_icone'        => $data['picture']['data']['url'],
                            'user_sexo_id'         => (($data['gender']=='male')?('1'):('2')),
                            'user_data_login'   => time(),
                            'user_data_ini'     => time(),
                            'user_data_alt'     => time(),
                            'user_id_facebook'  => $data['id'],
                        );
                        
                        $t->insert($user);
                        
                        $mail = new Default_Model_Mail();
                        $mail->enviar($user['user_apelido'], $user['user_email'], 'Confirmação de cadastro.', '
                            Olá, ' . $user['user_apelido'] . ', seja bem-vindo! <br/> <br/>

                            Seu cadastro foi realizado com sucesso!<br/> <br/>

                            Sua conta foi criada através do Facebook, mas você também poderá fazer login no site
                            diretamente apenas utilizando o seu endereço de e-mail e esta senha: <b>' . $senha . '</b>.<br/> <br/> <br/> <br/>

                            <i><b>Se você não gostou se sua senha para acesso direto não se preocupe!<b><br/>
                            Você pode alterar esta senha para uma de sua preferência a qualquer momento, no site.</i>
                        ');
                        
                        if($this->login($user, 'Facebook')){
                            $this->_redirect('/');
                        }else{
                            //$this->_redirect('/area-restrita');
                        }
                    }
                }
            }else{
                //$this->dialogo->add('Erro', 'A autenticação não foi realizada.');
            }
        } else {
            //$this->dialogo->add('Erro', 'A autenticação não foi realizada.');
        }
        $this->dialogo->add('Erro', 'A autenticação não foi realizada.');
        $this->_redirect('/');
    }

    public function entrarGoogleAction(){
        require_once APPLICATION_PATH . '/../library/Google/src/Google_Client.php';
        require_once APPLICATION_PATH . '/../library/Google/src/contrib/Google_Oauth2Service.php';
        
        $client = new Google_Client();
        $client->setApplicationName(DFL_NOME);
        $client->setClientId('728241411556.apps.googleusercontent.com');
        $client->setClientSecret('aVI3AWY07K6eOaSi1qPYd1MO');
        $client->setRedirectUri(DFL_URL . '/area-restrita/entrar-google');
      //$client->setDeveloperKey('insert_your_simple_api_key');
        
        $sys = new Zend_Session_Namespace('sistem');
        $get = $this->getRequest()->getParams();
        
        if (isset($get['code'])) {
            $client->authenticate($get['code']);
            $sys->tmp = $client->getAccessToken();
            $this->_redirect('/area-restrita/entrar-google');
        }
        
        if (isset($sys->tmp)) {
            $client->setAccessToken($sys->tmp);
        }
        
        if ($client->getAccessToken()) {
            $oauth2 = new Google_Oauth2Service($client);
            $data = $oauth2->userinfo->get();
            if($data['id']){
                //$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
                
                $db = Zend_Registry::get('db');
                $tmp = $db->fetchRow('SELECT * FROM dfl_user WHERE user_id_google=' . $db->quoteInto('?', $data['id']));
                if(isset($tmp) && $tmp['user_id'] > 0){
                    $user = array(
                        'user_apelido'  => $data['given_name'],
                        'user_nome'     => $data['name'],
                        'user_sexo_id'     => (($data['gender']=='male')?('1'):('2')),
                    );
                    $img = filter_var($data['picture'], FILTER_VALIDATE_URL);
                    if(isset($img)){
                        $user['user_icone'] = $img . '?sz=50';
                    }
                    $db->update('dfl_user', $user, 'user_id=' . $tmp['user_id']);
                    
                    if($this->login($tmp, 'Google')){
                        $this->_redirect('/');
                    }else{
                        //$this->_redirect('/area-restrita');
                    }
                }else{
                    $tmp = $db->fetchRow('SELECT * FROM dfl_user WHERE user_email=' . $db->quoteInto('?', $data['email']));
                    if(isset($tmp) && $tmp['user_id'] > 0){
                        if($tmp['user_id_google'] > 0){
                            $this->dialogo->add('Erro', 'Este e-mail já está sendo utilizado por outro usuário.');
                            $this->dialogo->add('Alerta', 'Se este e-mail lhe pertence entre em contato conosco para solucionarmos o problema.');
                        }else{
                            $user = array(
                                'user_apelido'  => $data['given_name'],
                                'user_nome'     => $data['name'],
                                'user_sexo_id'     => (($data['gender']=='male')?('1'):('2')),
                                'user_id_google'=> $data['id'],
                            );
                            $img = filter_var($data['picture'], FILTER_VALIDATE_URL);
                            if(isset($img)){
                                $user['user_icone'] = $img . '?sz=50';
                            }
                            $db->update('dfl_user', $user, 'user_id=' . $tmp['user_id']);
                            if($this->login($tmp, 'Google')){
                                $this->_redirect('/');
                            }else{
                                //$this->_redirect('/area-restrita');
                            }
                        }
                    }else{
                        $t = new Default_Model_Tabela_Usuario();
                        $senha = $t->getNovaSenha();
                        $user = array(
                            'user_status_id'       => '1',
                            'user_tipo_id'      => '1',
                            'user_email'        => $data['email'],
                            'user_senha'        => sha1($senha),
                            'user_apelido'      => $data['given_name'],
                            'user_nome'         => $data['name'],
                            'user_icone'        => $data['picture'],
                            'user_sexo_id'         => (($data['gender']=='male')?('1'):('2')),
                            'user_data_login'   => time(),
                            'user_data_ini'     => time(),
                            'user_data_alt'     => time(),
                            'user_id_google'  => $data['id'],
                        );
                        $t->insert($user);
                        
                        $mail = new Default_Model_Mail();
                        $mail->enviar($user['user_apelido'], $user['user_email'], 'Confirmação de cadastro.', '
                            Olá, ' . $user['user_apelido'] . ', seja bem-vindo! <br/> <br/>

                            Seu cadastro foi realizado com sucesso!<br/> <br/>

                            Sua conta foi criada através do Facebook, mas você também poderá fazer login no site
                            diretamente apenas utilizando o seu endereço de e-mail e esta senha: <b>' . $senha . '</b>.<br/> <br/> <br/> <br/>

                            <i><b>Se você não gostou se sua senha para acesso direto não se preocupe!<b><br/>
                            Você pode alterar esta senha para uma de sua preferência a qualquer momento, no site.</i>
                        ');
                        
                        if($this->login($user, 'Google')){
                            $this->_redirect('/');
                        }else{
                            //$this->_redirect('/area-restrita');
                        }
                    }
                }
            }else{
                //$this->dialogo->add('Erro', 'A autenticação não foi realizada.');
            }
        } else {
            //$this->dialogo->add('Erro', 'A autenticação não foi realizada.');
        }
        $this->dialogo->add('Erro', 'A autenticação não foi realizada.');
        $this->_redirect('/');
    }

    public function entrarTwitterAction(){
        
    }

    public function entrarLinkedinAction(){
        
    }
    
    public function sairAction() {
        // FACEBOOK //
        require APPLICATION_PATH . '/../library/Facebook/facebook.php';
        $facebook = new Facebook(array(
            'appId'  => '484204754972513',
            'secret' => '2a2b335c0216801cb706c837533bba4c',
            'fileUpload' => true,
        ));
        $facebook->getLogoutUrl();
        $facebook->destroySession();
        
        // GOOGLE //
        
        // TWITTER //
        
        // LINKEDIN //
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $historico = new Default_Model_Tabela_Historico('his_login');
            $historico->newLog(10);
            Zend_Auth::getInstance()->clearIdentity();
            $this->dialogo->add('Ok', 'Seu acesso foi encerrado corretamente !');
        }
        $this->_redirect('/');
    }
    
    // MÉTODOS //
    
    protected function login($get, $tpLogin='Tradicional') {
        $historico = new Default_Model_Tabela_Historico('his_login');
        if ($get['user_email'] != '' && $get['user_senha'] != '') {
            $db = Zend_Registry::get('db');
            if($tpLogin == 'Tradicional'){
                $authAdap = new Zend_Auth_Adapter_DbTable($db, 'dfl_user', 'user_email', 'user_senha', 'SHA1(?)');
            }else{
                $authAdap = new Zend_Auth_Adapter_DbTable($db, 'dfl_user', 'user_email', 'user_senha');
            }
            $authAdap->setIdentity($get['user_email'])->setCredential($get['user_senha']);
            $res = $authAdap->authenticate();

            if ($res->isValid()) {
                $auth = Zend_Auth::getInstance();
                $data = $authAdap->getResultRowObject(null, 'user_senha');
                $data->tipo_login = $tpLogin;
                if ($data->user_status_id == 1 || $data->user_status_id == 9) {
                    $data->user_bloqueios = $data->user_permissoes = array();

//                        $lista = $db->fetchAll('SELECT * FROM dfl_user_bloqueios WHERE user_id=' . $data->user_id);
//                        foreach ($lista as $value) {
//                            $data->user_bloqueios[$value['func_id']] = $value['func_id'];
//                        }
//                        
//                        if($data->user_tipo > AUTH_CADASTRADO){
//                            $lista = $db->fetchAll('SELECT * FROM dfl_user_permissoes WHERE user_id=' . $data->user_id);
//                            foreach ($lista as $value) {
//                                $data->user_permissoes[$value['func_id']] = $value['func_id'];
//                            }
//                        }

                    if($data->user_municipio_id > 0){
                        $res = $db->fetchRow('SELECT * FROM pu_estado E, pu_municipio M WHERE M.municipio_id=' . $data->user_municipio_id . ' && E.estado_id=M.municipio_estado_id');
                        if(isset($res['estado_id']) && $res['estado_id'] > 0){
                            $this->localidade->estado_id        = $res['estado_id'];
                            $this->localidade->estado_sigla     = $res['estado_sigla'];
                            $this->localidade->estado_nome      = $res['estado_nome'];
                            $this->localidade->municipio_id     = $res['municipio_id'];
                            $this->localidade->municipio_nome   = $res['municipio_nome'];
                        }
                    }
                    
                    $auth->getStorage()->write($data);
                    $historico->newLog(9);
                    $this->dialogo->add('Ok', 'Você foi autenticado com sucesso!');
                    
                    if($data->user_status_id == 2 || $data->user_status_id == 9){
                        $db->update('dfl_user', array('user_status_id' => 1, 'user_data_login' => time()), 'user_id=' . $data->user_id);
                        if($data->user_status_id == 2){
                           $this->dialogo->add('Alerta', 'Sua conta foi recuperada, mas as informações relacionadas a ela não podem ser recuperadas.'); 
                        }
                    }else{
                        $db->update('dfl_user', array('user_data_login' => time()), 'user_id=' . $data->user_id);
                    }

                    ob_start();
                        echo '<table border=1>';
                        echo '<tr><th colspan="2" style="background:#000; color:#fff">Dados da Conta</td></tr>';
                        echo '<tr><td>TIPO</td><td>' . $tpLogin . '</td></tr>';
                        echo '<tr><td>NOME</td><td>' . $data->user_nome . '</td></tr>';
                        echo '<tr><td>APELIDO</td><td>' . $data->user_login . '</td></tr>';
                        echo '<tr><td>E-MAIL</td><td>' . $data->user_email . '</td></tr>';

                        echo '<tr><th colspan="2" style="background:#000; color:#fff">Dados do Cliente</td></tr>';
                        echo '<tr><td>UNIQUE_ID</td><td>' . $_SERVER['UNIQUE_ID'] . '</td></tr>';
                        echo '<tr><td>REQUEST_TIME</td><td>' . date('h:i:s \d\e d/m/Y', $_SERVER['REQUEST_TIME']) . '</td></tr>';
                        echo '<tr><td>REMOTE_ADDR</td><td>' . $_SERVER['REMOTE_ADDR'] . '</td></tr>';
                        echo '<tr><td>REMOTE_PORT</td><td>' . $_SERVER['REMOTE_PORT'] . '</td></tr>';
                        echo '<tr><td>REQUEST_METHOD</td><td>' . $_SERVER['REQUEST_METHOD'] . '</td></tr>';
                        echo '<tr><td>REQUEST_URI</td><td>' . $_SERVER['REQUEST_URI'] . '</td></tr>';
                        echo '<tr><td>SCRIPT_FILENAME</td><td>' . $_SERVER['SCRIPT_FILENAME'] . '</td></tr>';
                        echo '<tr><td>SCRIPT_NAME</td><td>' . $_SERVER['SCRIPT_NAME'] . '</td></tr>';
                        echo '<tr><td>REDIRECT_URL</td><td>' . $_SERVER['REDIRECT_URL'] . '</td></tr>';
                        echo '<tr><td>REDIRECT_STATUS</td><td>' . $_SERVER['REDIRECT_STATUS'] . '</td></tr>';
                        echo '<tr><td>HTTP_HOST</td><td>' . $_SERVER['HTTP_HOST'] . '</td></tr>';
                        echo '<tr><td>HTTP_ACCEPT</td><td>' . $_SERVER['HTTP_ACCEPT'] . '</td></tr>';
                        echo '<tr><td>HTTP_ACCEPT_ENCODING</td><td>' . $_SERVER['HTTP_ACCEPT_ENCODING'] . '</td></tr>';
                        echo '<tr><td>HTTP_ACCEPT_LANGUAGE</td><td>' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '</td></tr>';
                        echo '<tr><td>HTTP_CONNECTION</td><td>' . $_SERVER['HTTP_CONNECTION'] . '</td></tr>';
                        echo '<tr><td>HTTP_USER_AGENT</td><td>' . $_SERVER['HTTP_USER_AGENT'] . '</td></tr>';

                        echo '<tr><th colspan="2" style="background:#000; color:#fff">Dados do Servidor</td></tr>';
                        echo '<tr><td>SERVER_ADDR</td><td>' . $_SERVER['SERVER_ADDR'] . '</td></tr>';
                        echo '<tr><td>APPLICATION_ENV</td><td>' . $_SERVER['APPLICATION_ENV'] . '</td></tr>';
                        echo '<tr><td>DOCUMENT_ROOT</td><td>' . $_SERVER['DOCUMENT_ROOT'] . '</td></tr>';
                        echo '<tr><td>GATEWAY_INTERFACE</td><td>' . $_SERVER['GATEWAY_INTERFACE'] . '</td></tr>';
                        echo '<tr><td>SERVER_ADMIN</td><td>' . $_SERVER['SERVER_ADMIN'] . '</td></tr>';
                        echo '<tr><td>SERVER_NAME</td><td>' . $_SERVER['SERVER_NAME'] . '</td></tr>';
                        echo '<tr><td>SERVER_PORT</td><td>' . $_SERVER['SERVER_PORT'] . '</td></tr>';
                        echo '<tr><td>SERVER_PROTOCOL</td><td>' . $_SERVER['SERVER_PROTOCOL'] . '</td></tr>';
                        echo '<tr><td>SERVER_SIGNATURE</td><td>' . $_SERVER['SERVER_SIGNATURE'] . '</td></tr>';
                        echo '<tr><td>SERVER_SOFTWARE</td><td>' . $_SERVER['SERVER_SOFTWARE'] . '</td></tr>';

                        echo '<tr><th colspan="2" style="background:#000; color:#fff">Dados do Adicionais</td></tr>';
                        echo '<tr><td>REDIRECT_APPLICATION_ENV</td><td>' . $_SERVER['REDIRECT_APPLICATION_ENV'] . '</td></tr>';
                        echo '<tr><td>REDIRECT_UNIQUE_ID</td><td>' . $_SERVER['REDIRECT_UNIQUE_ID'] . '</td></tr>';
                        echo '<tr><td>PATH</td><td>' . $_SERVER['PATH'] . '</td></tr>';
                        echo '<tr><td>PHP_SELF</td><td>' . $_SERVER['PHP_SELF'] . '</td></tr>';
                        echo '<tr><td>QUERY_STRING</td><td>' . $_SERVER['QUERY_STRING'] . '</td></tr>';
                        echo '<tr><td>argv</td><td>' . $_SERVER['argv'] . '</td></tr>';
                        echo '<tr><td>argc</td><td>' . $_SERVER['argc'] . '</td></tr>';
                        echo '</table>';
                        $saida = ob_get_contents();
                    ob_end_clean();

                    $mail = new Default_Model_Mail();
                    $mail->enviar(DFL_NOME, 'bsi.alexoliveira@gmail.com', 'LOGIN | ' . date('d/m/Y'), date('d/m/Y H:i:s') . ' <hr/> ' . $saida);

                    return true;
                } elseif($data->user_status_id == 0) { // NOVO //
                    $historico->newLog(5, $data->user_id);
                    $this->dialogo->add('Alerta', 'Sua conta ainda não foi confirmada. Para confirmá-la você precisará clicar no <b>link de confirmação</b> que enviamos para o seu e-mail.');
                    $this->dialogo->add('Infor', 'Para receber um novo e-mail de confirmação de conta <b><a href="/area-restrita/confirmar-cadastro/solicitacao/' . sha1($data->user_email) . '-' . $data->user_id . '">clique aqui</a></b>.');
                } elseif($data->user_status_id == 3) { // BLOQUEADO //
                    $historico->newLog(6, $data->user_id);
                    $this->dialogo->add('Alerta', 'Sua conta está bloqueada! Contate o administrador.');
                } elseif($data->user_status_id == 4) { // REMOVIDO //
                    $historico->newLog(7, $data->user_id);
                    $this->dialogo->add('Erro', 'Sua conta foi removida pelo administrador.');
                } else { // DESATIVADO e OUTROS //
                    $historico->newLog(8, $data->user_id);
                    $this->dialogo->add('Erro', 'Acesso Negado !');
                }
            } else {
                $usrTmp = $db->fetchRow("SELECT * FROM dfl_user WHERE " . $db->quoteInto('user_email=?', $get['user_email']));
                if (isset($usrTmp['user_id'])) {
                    $historico->newLog(3);
                } else {
                    $historico->newLog(4, $get['user_email']);
                }
                $this->dialogo->add('Erro', 'Acesso Negado !');
            }
        } else {
            $historico->newLog(2);
            $this->dialogo->add('Erro', 'Acesso Inválido !');
        }
        return false;
    }
    
}