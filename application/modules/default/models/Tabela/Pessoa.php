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
class Default_Model_Tabela_Pessoa extends Zend_Db_Table_Abstract {

    public $_db;
    protected $_name = 'pu_pessoa';
    protected $_primary = array('pessoa_id');
    protected $dialogo = null;
    protected $data = array(
        'get'                   => array(),
        'pagina'                => array(
            'atual'             => 1,
            'max-itens'         => 18,
            'qtd-itens'         => 0,
            'total-itens'       => 0,
            'fim'               => 1,
            'ordem'             => array('pessoa_nome')
        ),
        'resultado'             => array()
    );
    
    // CONSTRUTOR //

    public function __construct($config = array()) {
        $sSistem = new Zend_Session_Namespace('sistem');
        $this->dialogo = $sSistem->dialogo;
        if(isset($config['max-itens']) && is_numeric($config['max-itens']) && $config['max-itens'] % 1 == 0){
            $this->data['pagina']['max-itens'] = $config['max-itens'];
        }
        parent::__construct($config);
    }

    // AÇÕES //

    public function buscar(array $get = array()) {
        $this->data['get'] = $get;
        
        // WHERE //
        $where = array();
        if(isset($get['palavra-chave']) && strlen($get['palavra-chave']) > 2){
            $termos = explode(' ', $get['palavra-chave']);
            foreach ($termos as $termo) {
                $tmp = $this->_db->fetchRow('SELECT * FROM pu_pessoa_termo  WHERE termo_nome=' . $this->_db->quoteInto('?', $termo));
                if($tmp){
                    $where[] = $tmp['termo_id'];
                }
            }
            
            if(count($where) > 0){
                $tmp = $this->_db->fetchAll('
                    SELECT TI.pessoa_id, count(*) qtd
                    FROM pu_pessoa_termo_indice TI
                    WHERE TI.termo_id IN ( ' . implode(', ', $where) . ' )
                    GROUP BY TI.pessoa_id
                    HAVING qtd > ' . (count($where)*0.5) . '
                    ORDER BY qtd DESC
                ');
                if($tmp){
                    $qtd = count($tmp);

                    // PAGINA //
                    if(isset($get['pagina']) && $get['pagina'] > 0){
                        $this->data['pagina']['atual'] = $get['pagina'];
                    }

                    // QTD & LIMITE //
                    $this->data['pagina']['total-itens'] = $qtd;
                    $this->data['pagina']['fim'] = ceil($qtd / $this->data['pagina']['max-itens']);
                    
                    // PREPARA LISTA DE ID's //
                    $ini = ($this->data['pagina']['atual'] - 1) * $this->data['pagina']['max-itens'];
                    if($ini > $qtd){
                        $ini = ($this->data['pagina']['fim'] - 1) * $this->data['pagina']['max-itens'];
                    }
                    
                    $ids = array();
                    for($i=0; $i<$this->data['pagina']['max-itens']; $i++){
                        if(isset($tmp[$ini])){
                            $ids[$tmp[$ini]['pessoa_id']] = $tmp[$ini]['pessoa_id'];
                        }
                        $ini++;
                    }
                    unset($tmp);
                    
                    // BUSCA //
                    $this->data['resultado'] = $ids;
                    $tmp = $this->_db->fetchAll('SELECT * FROM pu_pessoa P WHERE P.pessoa_id IN ( ' . implode(', ', $ids) . ' )');
                    foreach ($tmp as $value) {
                        $this->data['resultado'][$value['pessoa_id']] = $value;
                    }
                    $this->data['pagina']['qtd-itens'] = count($tmp);
                }
            }
            return $this->data;
        }else{
            $this->dialogo->add('Infor', 'Escreva o nome da pessoa que você procura, com no mínimo 3 letras!');
            $tmp = $this->_db->fetchAll('SELECT * FROM pu_pessoa P LIMIT 0, ' . $this->data['pagina']['max-itens']);
            $this->data['pagina']['total-itens'] = count($tmp);
            $this->data['pagina']['qtd-itens'] = $this->data['pagina']['total-itens'];
            $this->data['resultado'] = $tmp;
            return $this->data;
        }
    }

    public function detalhar($id){
        return $this->fetchRow($this->_db->quoteInto('pessoa_id=?', $id));
    }

    public function detalharUrl($url){
        return $this->fetchRow($this->_db->quoteInto('pessoa_url=?', $url));
    }

    public function detalharCpf($cpf){
        return $this->fetchRow($this->_db->quoteInto('pessoa_cpf=?', $cpf));
    }

    public function detalharTituloEleitor($titulo){
        return $this->fetchRow($this->_db->quoteInto('pessoa_titulo_eleitor=?', $titulo));
    }
    
}