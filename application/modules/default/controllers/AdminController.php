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
class Default_AdminController extends Alex_Controller {

    public function init() {
        if (Zend_Auth::getInstance()->hasIdentity() && Zend_Auth::getInstance()->getIdentity()->user_tipo_id == 3) {
        }else{
            $this->_redirect('/');
        }
        parent::init();
        $this->local[] = '<a href="/admin">Administração</a>';
    }

    public function indexAction() {
        
    }

    public function relacionarPessoasAction() {
        if(Zend_Auth::getInstance()->getIdentity()->user_tipo_id!=3){
            $this->_redirect('/');
        }
        
        if($this->getRequest()->isPost()){
            $post = $this->getRequest()->getPost();
            if(isset($post['pessoa_cd_origem']) && isset($post['pessoa_id'])){
                if(is_numeric($post['pessoa_cd_origem']) && $post['pessoa_cd_origem'] % 1 == 0 && $post['pessoa_cd_origem'] > 0){
                    if(is_numeric($post['pessoa_id']) && $post['pessoa_id'] % 1 == 0 && $post['pessoa_id'] > 0){
                        $db = Zend_Registry::get('db');
                        $res = $db->update('pu_pessoa_de_para', array(
                            'pessoa_id' => $post['pessoa_id']
                        ), 'pessoa_id=0 && pessoa_cd_origem=' . $db->quoteInto('?', $post['pessoa_cd_origem']));
                        if($res){
                            $this->dialogo->add('Ok', 'Relacionamento realizado com sucesso. Obrigado!!!');
                        }else{
                            $this->dialogo->add('Alerta', 'Desculpe. Não foi possível salvar o relacionamento sugerido.');
                        }
                    }
                }
            }
            $this->_redirect('/user/relacionar-pessoas');
        }
        
        $this->local[] = '<a href="/user/relacionar-pessoas">Relacionar Pessoas</a>';
        $t = new Default_Model_Tabela_Pessoa(array('max-itens' => 60));
        $this->view->pessoa = $t->_db->fetchRow('SELECT *, count(*) as qtd FROM pu_pessoa_de_para WHERE pessoa_id=0 LIMIT 0, 1');
        
        if($this->view->pessoa){
            $nome = substr($this->view->pessoa['pessoa_nome'], 0, strpos($this->view->pessoa['pessoa_nome'], '-'));
            $this->local[] = $nome;
            $this->view->Lista = $t->buscar(array('palavra-chave' => $nome));
            if(!($this->view->Lista['pagina']['qtd-itens'] > 0)){
                $this->dialogo->add('Alerta', 'Nenhuma pessoa com nome semelhante foi encontrada.');
            }
        }
    }
    
    // AJAX //
    
}