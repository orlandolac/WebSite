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
class Default_Model_Tabela_Usuario extends Alex_Tabela {

    protected $_name = 'dfl_user';
    protected $primary = array('user_id');
    protected $busca_ordens = array('user_nome');
    protected $cps = array(
        'user_id' => '',
        'user_status_id' => 0,
        'user_tipo_id' => 1,
        'user_email' => '',
        'user_senha' => '',
        'user_apelido' => '',
        'user_nome' => '',
        'user_icone' => '',
        'user_data_nascimento' => '',
        'user_sexo_id' => 0,
        'user_data_login' => '',
        'user_data_ini' => '',
        'user_data_alt' => ''
    );

    // FUNÇÕES //

    public function detalhar($user_id) {
        return parent::detalhar('user_id=' . $this->_db->quoteInto('?', $user_id));
    }

    public function detalharEmail($user_email) {
        return parent::detalhar('user_email=' . $this->_db->quoteInto('?', $user_email));
    }

    public function salvar() {
        if (strlen($this->cps['user_email']) > 0) {
            $res = $this->fetchRow('user_status_id=2 && ' . $this->_db->quoteInto('user_email=?', $this->cps['user_email']));
            if ($res) {
                $this->cps['user_id'] = $res['user_id'];
                if ($this->eValido(false)) {
                    $this->cps['user_senha'] = sha1($this->cps['user_senha']);
                    unset($this->cps['user_data_ini']);
                    unset($this->cps['user_data_alt']);
                    unset($this->cps['user_data_login']);
                
                    $where = 'user_id=' . $this->cps['user_id'];
                    if ($where) {
                        if (parent::update($this->cps, $where)) {
                            $this->dialogo->add('Ok', 'Recadastro realizado com sucesso!');
                            $this->posAlterar($where);
                            return true;
                        }else{
                            $this->dialogo->add('Alerta', 'Não foi possível fazer o seu recadastrar.');
                            return false;
                        }
                    }
                } else {
                    $this->dialogo->add('Alerta', 'Nenhuma alteração foi realizada.');
                }
            }else{
                return parent::salvar();
            }
        }else{
            return false;
        }
    }
    
    public function confirmarCadastro(){
        if($this->eSalvo()){
            $mail = new Default_Model_Mail();
            $mail->enviar($this->cps['user_apelido'], $this->cps['user_email'], 'Confirmação de cadastro.', '
                Olá, ' . $this->cps['user_apelido'] . ', seja bem-vindo! <br/> <br/>
                
                Seu cadastro foi realizado com sucesso, mas ainda não foi confirmado!<br/> <br/>
                
                Para confirmar o cadastro e começar a utilizar os nossos serviços clique no link a seguir: <br/>
                <b><a href="' . DFL_URL . '/area-restrita/confirmar-cadastro/usuario/' . sha1($this->cps['user_email']) . '">CONFIRMAR CADASTRO</a></b>.<br/> <br/> <br/> <br/>
                
                <i><b>Se não foi você quem cadastrou seu e-mail não se preocupe!</b><br/>
                Esta conta ainda não está ativada e você pode solicitar que a mesma seja
                removida completamente através do formulário de contado, mas não faça isso
                antes de conhecer nossos serviços.</i>
            ');
            $this->dialogo->add('Alerta', 'Agora você só precisa entrar no seu e-mail (<b>' . $this->cps['user_email'] . '</b>) e <b>confirmar o cadastro</b> através da mensagem que lhe enviamos.');
        }else{
            $this->dialogo->add('Erro', 'A mensagem de confirmação não será enviada porque o e-mail não foi encontrado em nosso banco de dados.');
        }
    }

    public function confirmarNovaSenha(){
        if($this->eSalvo()){
            if($this->cps['user_status_id'] == 1 || $this->cps['user_status_id'] == 9){
                $time = time();
                $key = sha1($this->cps['user_email']) . '-' . $this->cps['user_id'];
                $key = substr($key, 0, 10) . $time . substr($key, 10);

                $mail = new Default_Model_Mail();
                $mail->enviar($this->cps['user_apelido'], $this->cps['user_email'], 'Solicitação de nova senha.', 
                    $this->cps['user_apelido'] . ', recebemos uma solicitação de nova senha para a sua conta no ' . DFL_NOME . '. <br/> <br/>

                    Para confirmar o pedido e receber uma nova senha clique no link a seguir: <br/>
                    <b><a href="' . DFL_URL . '/area-restrita/esqueci-senha/usuario/' . $key . '">CONFIRMAR SOLICITAÇÃO DE NOVA SENHA</a></b>.<br/> <br/> <br/> <br/>

                    <b>Atenção!</b><br/>
                    <ul>
                        <li>Após clicar no link de confirmação uma nova senha será gerada automáticamente e não seja mais possível acessar a sua conta com a antiga senha.</li>
                        <li>Este e-mail foi gerado ás, ' . date('H:i:s \d\e d/m/Y', $time) . ' e <b>perderá a validade em duas horas</b>.</li>
                    </ul> <br/> <br/> <br/>

                    <i><b>Caso você não tenha soliticado uma nova senha não se preocupe!</b><br/>
                    Este e-mail está programado para expirar em duas horas, caso não seja utilizado. Se dentro
                    ou após este período você encontrar algum problema para acessar a sua conta entre em contato
                    conosco através do formulário de contado do site.</i>
                ');

                $this->update(array('user_status_id' => 9), 'user_id=' . $this->_db->quoteInto('?', $this->cps['user_id']));
                $this->dialogo->add('Ok', 'Enviamos para você uma mensagem de <b>confirmação de pedido de nova senha</b> no endereço de e-mail <b>' . $this->cps['user_email'] . '</b>.');
            }elseif($this->cps['user_status_id'] == 0){
                $this->dialogo->add('Alerta', 'A alteração de senha está temporáriamente indisponível para este usuário</b>. Esta conta ainda não foi confirmada.');
            }elseif($this->cps['user_status_id'] == 2){
                $this->dialogo->add('Alerta', 'A alteração de senha está temporáriamente indisponível para este usuário</b>. Esta conta foi desativada pelo usuário.');
            }elseif($this->cps['user_status_id'] == 3){
                $this->dialogo->add('Alerta', 'A alteração de senha está temporáriamente indisponível para este usuário</b>. Esta conta está bloqueada.');
            }elseif($this->cps['user_status_id'] == 4){
                $this->dialogo->add('Alerta', 'A alteração de senha está temporáriamente indisponível para este usuário</b>. Esta conta foi removida pela administração.');
            }else{
                $this->dialogo->add('Alerta', 'A alteração de senha está temporáriamente indisponível para este usuário</b>.');
            }
        }else{
            $this->dialogo->add('Erro', 'A mensagem de confirmação não será enviada porque o e-mail não foi encontrado em nosso banco de dados.');
        }
    }

    public function setNovaSenha($solicitacao) {
        if($this->eSalvo()){
            $time = substr($solicitacao, 10, 10);
            if((time() - $time) > (2 * 60 * 60)){
                $this->dialogo->add('Erro', 'Esta solicitação não é mais válida.');
                $this->dialogo->add('Alerta', 'Caso você ainda precise auterar a sua senha, preencha o campo a baixo para solicitar novamente.');
            }else{
                if($this->cps['user_status_id'] == 9){
                    $solicitacao = substr($solicitacao, 0, 10) . substr($solicitacao, 20);
                    $parts = explode('-', $solicitacao);
                    $user_id = $parts[(count($parts)-1)];

                    if($this->cps['user_id'] == $user_id){
                        $solicitacao = substr($solicitacao, 0, ((strlen($user_id)+1)*-1));

                        if(sha1($this->cps['user_email']) == $solicitacao){
                            $alfa = array('q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'ç', 'z', 'x', 'c', 'v', 'b', 'n', 'm');
                            $sinb = array('!', '@', '#', '$', '%', '&', '(', ')', '_', '{');

                            $newSenha = '';
                            for($i=1; $i<7; $i++){
                                if(rand(0, 1)){
                                    $newSenha .= $alfa[rand(0, 26)];
                                }elseif(rand(0, 1)){
                                    $newSenha .= rand(0, 9);
                                }else{
                                    $newSenha .= $sinb[rand(0, 9)];
                                }
                            }

                            $mail = new Default_Model_Mail();
                            $mail->enviar($this->cps['user_apelido'], $this->cps['user_email'], 'Sua nova senha no ' . DFL_NOME, '
                                Ok, ' . $this->cps['user_apelido'] . '! <br/> <br/>
                                A partir de agora sua senha será: <u><b>' . $newSenha . '</b></u><br/> <br/>
                                    
                                <b>RECOMENDAÇÕES</b><br/>
                                <ul>
                                    <li>Decore sua nova senha e exclua este e-mail o mais rápido possível.</li>
                                    <li>Esta senha é aleatória e caso você não tenha gostado dela utilize-a
                                        para acessar sua conta no ' . DFL_NOME . ' e colocar a senha que preferir.</li>
                                    <li>Caso tenha tido algum problema de invasão na sua conta crie 
                                        uma senha que você nunca utilizou, seguindo nossas recomendações.</li>
                                    <li>Se tiver algum problema já sabe, conte conosco e entre em contato
                                        através dos formulário de contado do site.</li>
                                </ul>
                            ');

                            $this->update(array('user_senha' => sha1($newSenha), 'user_status_id' => 1), 'user_id=' . $this->cps['user_id']);
                            $this->dialogo->add('Ok', 'Sua senha foi alterada com sucesso! A nova senha foi enviada para o seu e-mail.');
                            return true;
                        }else{
                            $this->dialogo->add('Erro', 'Esta solicitação não é mais válida. {sem email}');
                        }
                    }else{
                        $this->dialogo->add('Erro', 'Esta solicitação não é mais válida. {outra conta}');
                    }
                }else{
                    $this->dialogo->add('Erro', 'Esta solicitação não é mais válida. {sem solicitacao}');
                }
            }
        }else{
            $this->dialogo->add('Erro', 'Esta solicitação não é mais válida. {sem conta}');
        }
        return false;
    }

    public function alterarSenha($get){
        if($this->eSalvo()){
            if(isset($get['user_senha_atual']) && isset($get['user_senha']) && isset($get['user_senha_repitida'])){
                if(strlen($get['user_senha']) > 5){
                    if($get['user_senha'] == $get['user_senha_repitida']){
                        if(isset($this->cps['user_senha']) && $this->cps['user_senha'] == sha1($get['user_senha_atual'])){
                            if($this->update(array('user_senha' => sha1($get['user_senha'])), 'user_id=' . $this->_db->quoteInto('?', $this->cps['user_id']))){
                                $this->dialogo->add('Ok', 'Sua senha foi alterada com sucesso !');
                                return true;
                            }else{
                                $this->dialogo->add('Alerta', 'A senha atual <b>não foi alterada</b>.');
                            }
                        }else{
                            $this->dialogo->add('Erro', 'A <b>senha atual</b> informada é inválida.');
                        }
                    }else{
                        $this->dialogo->add('Erro', 'A <b>nova senha</b> informada não confere com a sua <b>repetição</b>.');
                    }
                }else{
                    $this->dialogo->add('Erro', 'A <b>nova senha</b> informada não é válida.');
                }
            }else{
                $this->dialogo->add('Erro', 'Solicitação inválida.');
            }
        }else{
            $this->dialogo->add('Erro', 'Usuário não cadastro.');
        }
        return false;
    }

    public function fecharConta($get){
        if($this->eSalvo()){
            if(isset($get['user_senha']) && strlen($get['user_senha']) > 5){
                if($this->cps['user_senha'] == sha1($get['user_senha'])){
                    $data = array(
                        'user_status_id' => '2',
                        'user_tipo_id' => '1',
                        'user_senha' => '',
                        'user_apelido' => 'PoupaVocê.com',
                        'user_nome' => 'PoupaVocê.com',
                        'user_icone' => '',
                        'user_data_nascimento' => '',
                        'user_data_login' => time(),
                        'user_data_alt' => time(),
                    );
                    if($this->update($data, 'user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id)){
                        $this->_db->delete('dfl_user_endereco', 'user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id);
                        $this->dialogo->add('Infor', 'Gostaríamos muito de saber dos motivos que lhe fizeram fechar a conta, compartilhe conosco esta informação através do <a href="/contato"><u><b>formulário de contato</b></u></a>, com isso poderemos melhorar nossos serviços para quem sabe um dia você voltar.');
                        $this->dialogo->add('Ok', 'Sua conta foi fechada com sucesso !');
                        return true;
                    }else{
                        $this->dialogo->add('Erro', 'Não foi possível fechar a sua conta.');
                    }
                }else{
                    $this->dialogo->add('Erro', 'A senha informada é inválida.');
                }
            }else{
                $this->dialogo->add('Erro', 'Solicitação inválida.');
            }
        }else{
            $this->dialogo->add('Erro', 'Usuário não cadastro.');
        }
        return false;
    }

    // MÉTODOS //

    public function getNovaSenha(){
        $alfa = array('q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'ç', 'z', 'x', 'c', 'v', 'b', 'n', 'm');
        $sinb = array('!', '@', '#', '$', '%', '&', '(', ')', '_', '{');

        $newSenha = '';
        for($i=1; $i<7; $i++){
            if(rand(0, 1)){
                $newSenha .= $alfa[rand(0, 26)];
            }elseif(rand(0, 1)){
                $newSenha .= rand(0, 9);
            }else{
                $newSenha .= $sinb[rand(0, 9)];
            }
        }
        return $newSenha;
    }

    public function eSalvo() {
        if($this->cps['user_email'] != ''){
            $res = $this->fetchRow('user_status_id=2 && user_email=' . $this->_db->quoteInto('?', $this->cps['user_email']));
            if($res){
                $this->setCps(array('user_id' => $res['user_id']));
                return true;
            }
        }
        return parent::eSalvo();
    }

    public function eValido($novo = true) {
        $w = ($novo) ? ('') : ($this->_db->quoteInto(' user_id <> ? AND ', $this->cps['user_id']));
        $valido = 0;
        if (!strlen($this->cps['user_nome']) > 0) {
            $this->dialogo->add('Erro', 'O campo <b>"Nome"</b> é de preenchimento obrigatório.');
            $valido++;
        }
        if (strlen($this->cps['user_senha']) > 5) {
            if(is_numeric($this->cps['user_senha'])){
                $this->dialogo->add('Erro', 'O campo <b>"Senha"</b> deve misturar números e letras.');
                $valido++;
            }
        }else{
            $this->dialogo->add('Erro', 'O campo <b>"Senha"</b> é de preenchimento obrigatório.');
            $valido++;
        }
        if (strlen($this->cps['user_email']) > 0) {
            $res = $this->fetchRow($w . $this->_db->quoteInto(' user_email = ?', $this->cps['user_email']));
            if ($res) {
                $this->dialogo->add('Erro', 'Já existe uma usuário com o e-mail <b>"' . $this->cps['user_email'] . '"</b> no banco de dados.');
                $valido++;
            }
        } else {
            $this->dialogo->add('Erro', 'O campo <b>"E-Mail"</b> é de preenchimento obrigatório.');
            $valido++;
        }
        if ($valido) {
            return false;
        } else {
            return true;
        }
    }

    // GATILHOS //

    protected function preCadastrar() {
        $this->cps['user_senha'] = sha1($this->cps['user_senha']);
        $this->cps['user_data_ini'] = time();
        $this->cps['user_data_alt'] = time();
    }

    protected function posCadastrar() {
        $this->confirmarCadastro();
    }

    protected function preAlterar() {
        $res = $this->fetchRow('user_id=' . $this->_db->quoteInto('?', $this->cps['user_id']));
        if($res['user_senha'] == sha1($this->cps['user_senha'])){
            unset($this->cps['user_senha']);
            unset($this->cps['user_data_login']);
            unset($this->cps['user_data_ini']);
            unset($this->cps['user_data_alt']);
            return $this->_db->quoteInto('user_id=?', $this->cps['user_id']);
        }else{
            $this->dialogo->add('Erro', 'Senha inválida');
            return false;
        }
    }

    protected function posAlterar($where) {
        if($this->cps['user_status_id'] == 0){
            $this->confirmarCadastro();
        }
        $this->_db->update('dfl_user', array('user_data_alt' => time()), $where);
    }

    protected function posDeletar($where) {
        if ($this->cps['user_icone'] != '') {
            $link = PUBLIC_PATH . $this->cps['user_icone'];
            if (file_exists($link)) {
                unlink($link);
            }
        }
    }

}