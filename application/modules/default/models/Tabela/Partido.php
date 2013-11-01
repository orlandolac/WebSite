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
class Default_Model_Tabela_Partido extends Zend_Db_Table_Abstract {

    public $_db;
    protected $_name = 'pu_partido';
    protected $_primary = array('partido_id');
    protected $dialogo = null;
    protected $data = array(
        'get'                   => array(),
        'pagina'                => array(
            'atual'             => 1,
            'max-itens'         => 18,
            'qtd-itens'         => 0,
            'total-itens'       => 0,
            'fim'               => 1,
            'ordem'             => array('P.partido_status_id', 'P.partido_nome') // 'qtd DESC', 'C.cand_status_cand', 'C.cand_status_total',
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

    public function buscar(array $get) {
        $this->data['get'] = $get;
        
        // WHERE //
        $where = array();
        if(isset($get['palavra-chave']) && strlen($get['palavra-chave']) > 0){
            if(is_numeric($get['palavra-chave']) && $get['palavra-chave'] % 1 == 0){
                $tmp = $this->getDefaultAdapter()->quoteInto('?', $get['palavra-chave']);
                $where[] = '(P.partido_numero=' . $tmp . ' || P.partido_id=' . $tmp . ')';
            }else{
                $tmp = $this->getDefaultAdapter()->quoteInto('?', '%' . $get['palavra-chave'] . '%');
                $where[] = '(P.partido_nome like ' . $tmp . ' || P.partido_sigla like ' . $tmp . ')';
            }
        }
        
        $where[] = 'P.partido_id>0';
        $SQL = '';
        if(count($where) > 0){
            $SQL = ' WHERE ' . implode(' && ', $where);
        }
        
        $tmp = $this->_db->fetchRow('SELECT count(*) AS qtd FROM pu_partido P ' . $SQL);
        if ($tmp['qtd'] > 0) {
            // PAGINA //
            if(isset($get['pagina']) && $get['pagina'] > 0){
                $this->data['pagina']['atual'] = $get['pagina'];
            }
            
            // QTD & LIMITE //
            $this->data['pagina']['total-itens'] = $tmp['qtd'];
            $this->data['pagina']['fim'] = ceil($this->data['pagina']['total-itens'] / $this->data['pagina']['max-itens']);
        
            // BUSCA //
            $SQL = 'SELECT * FROM pu_partido P ' . $SQL;
            if(count($this->data['pagina']['ordem']) > 0){
                $SQL .= ' ORDER BY ' . implode(', ', $this->data['pagina']['ordem']);
            }
            
            $tmp = $this->_db->fetchAll($SQL . ' LIMIT ' . ($this->data['pagina']['atual'] - 1) * $this->data['pagina']['max-itens'] . ', ' . $this->data['pagina']['max-itens']);
            if(!(count($tmp) > 0)){
                $this->data['pagina']['atual'] = $this->data['pagina']['fim'];
                $tmp = $this->_db->fetchAll($SQL . ' LIMIT ' . ($this->data['pagina']['atual'] - 1) * $this->data['pagina']['max-itens'] . ', ' . $this->data['pagina']['max-itens']);
            }
        
            $this->data['pagina']['qtd-itens'] = count($tmp);
            $this->data['resultado'] = $tmp;
        }
        return $this->data;
    }
    
    public function detalhar($id){
        return $this->fetchRow($this->_db->quoteInto('partido_id=?', $id));
    }
    
    public function detalharUrl($url){
        return $this->fetchRow($this->_db->quoteInto('partido_url=?', $url));
    }
    
}