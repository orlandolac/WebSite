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
class Default_Model_Tabela_Contato extends Alex_Tabela {

    protected $_name = 'dfl_contato';
    protected $primary = array('contato_id');
    protected $busca_ordens = array('contato_data_ini');
    protected $cps = array(
        'contato_id' => '',
        'contato_user_id' => 0,
        'contato_status_id' => 1,
        'contato_nome' => '',
        'contato_email' => '',
        'contato_assunto' => '',
        'contato_mensagem' => '',
        'contato_data_ini' => ''
    );

    // FUNCIÇÕES //

    public function detalhar($contato_id) {
        return parent::detalhar('contato_id=' . $this->_db->quoteInto('?', $contato_id));
    }

    // MÉTODOS //

    public function eValido($novo = true) {
        $w = ($novo) ? ('') : ($this->_db->quoteInto('contato_id<>? AND ', $this->cps['contato_id']));
        $valido = 0;
        if(Zend_Auth::getInstance()->hasIdentity()){
            $auth = Zend_Auth::getInstance()->getIdentity();
            $this->cps['contato_user_id'] = $auth->user_id;
            $this->cps['contato_nome'] = $auth->user_nome;
            $this->cps['contato_email'] = $auth->user_email;
        }else{
            if (!strlen($this->cps['contato_nome']) > 0) {
                $this->dialogo->add('Erro', 'O campo <b>"Nome Completo"</b> é de preenchimento obrigatório.');
                $valido++;
            }
            if (strlen($this->cps['contato_email']) > 0) {
                $res = $this->_db->fetchRow('SELECT * FROM dfl_user WHERE user_email=' . $this->_db->quoteInto('?', $this->cps['contato_email']));
                if($res){
                    $this->cps['contato_user_id'] = $res['user_id'];
                    $this->cps['contato_nome'] = $this->cps['contato_nome'] . ' *';
                }
            }else{
                $this->dialogo->add('Erro', 'O campo <b>"E-Mail"</b> é de preenchimento obrigatório.');
                $valido++;
            }
        }
        if (!strlen($this->cps['contato_assunto']) > 0) {
            $this->dialogo->add('Erro', 'O campo <b>"Assunto"</b> é de preenchimento obrigatório.');
            $valido++;
        }
        if (!strlen($this->cps['contato_mensagem']) > 0) {
            $this->dialogo->add('Erro', 'O campo <b>"Mensagem"</b> é de preenchimento obrigatório.');
            $valido++;
        }
        if ($valido) {
            return false;
        } else {
            $this->cps['contato_data_ini'] = time();
            return true;
        }
    }

    public function eExcluivel() {
        return array($this->_db->quoteInto('contato_id = ?', $this->cps['contato_id']));
    }

    // ABSTRATOS //

    protected function posCadastrar() {
        $mail = new Default_Model_Mail();
        $mail->addTo(DFL_MAIL_CONTATO, utf8_decode(DFL_NOME));
        $mail->setSubject(utf8_decode($this->cps['contato_assunto']));
        $mail->setMensagem($this->cps['contato_mensagem']);
        $mail->setFrom($this->cps['contato_email'], $this->cps['contato_nome']);
        $mail->send();
    }

    public function preAlterar() {
        return $this->_db->quoteInto('contato_id=?', $this->cps['contato_id']);
    }

}