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
class Default_Model_Tabela_Historico extends Zend_Db_Table_Abstract {

    public function __construct($nome) {
        parent::__construct(array('name' => $nome));
    }

    public function newLog($his_acao, $his_detalhe = '') {
        if(is_array($his_detalhe)){
            ob_start();
            print_r($his_detalhe);
            $saida = ob_get_contents();
            ob_end_clean();
            $his_detalhe = $saida;
        }
        $data = array(
            'his_acao' => $his_acao,
            'his_data' => time(),
            'his_user_id' => 0,
            'his_user_ip' => $_SERVER['SERVER_ADDR'],
            'his_detalhe' => $his_detalhe
        );
        if(Zend_Auth::getInstance()->getIdentity()){
           $data['his_user_id'] = Zend_Auth::getInstance()->getIdentity()->user_id;
        }
        parent::insert($data);
    }

    public function update(array $data, $where) {}

    public function delete($where) {}

}