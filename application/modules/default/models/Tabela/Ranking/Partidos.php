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
class Default_Model_Tabela_Ranking_Partidos extends Zend_Db_Table_Abstract {

    public $_db;
    protected $_name = 'pu_partido_x_ranking';
    protected $_primary = array('rank_ano', 'partido_id');
    protected $dialogo = null;
    protected $data = array(
        'get'                   => array(),
        'pagina'                => array(
            'atual'             => 1,
            'max-itens'         => 20,
            'qtd-itens'         => 0,
            'total-itens'       => 0,
            'fim'               => 1,
            'ordem'             => array('R.rank_ano DESC', 'R.rank_qtd_pontos DESC', 'R.rank_qtd_positivos DESC', 'R.rank_qtd_negativos')
        ),
        'resultado'             => array()
    );
    
    // CONSTRUTOR //

    public function __construct($config = array()) {
        $sSistem = new Zend_Session_Namespace('sistem');
        $this->dialogo = $sSistem->dialogo;
        parent::__construct($config);
    }

    // AÇÕES //

    public function buscar(array $get) {
        $this->data['get'] = $get;
        
        // WHERE //
        $where = array();
        
//        if(isset($get['veiculo']) && is_numeric($get['veiculo']) && $get['veiculo'] > 0 && $get['veiculo'] % 1 == 0){
//            $where[] = 'N.noticia_veiculo_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['veiculo']);
//        }
        
        $SQL = '';
        if(count($where) > 0){
            $SQL = ' WHERE ' . implode(' && ', $where);
        }
        
        $tmp = $this->_db->fetchRow('SELECT count(*) AS qtd FROM pu_partido_x_ranking R ' . $SQL);
        if ($tmp['qtd'] > 0) {
            // PAGINA //
            if(isset($get['pagina']) && $get['pagina'] > 0){
                $this->data['pagina']['atual'] = $get['pagina'];
            }

            // QTD & LIMITE //
            $this->data['pagina']['total-itens'] = $tmp['qtd'];
            $this->data['pagina']['fim'] = ceil($this->data['pagina']['total-itens'] / $this->data['pagina']['max-itens']);

            // BUSCA //
            if($SQL == ''){
                $SQL = ' WHERE R.partido_id=P.partido_id ';
            }else{
                $SQL = ' && R.partido_id=P.partido_id ';
            }
            
            $SQL = 'SELECT * FROM pu_partido_x_ranking R, pu_partido P ' . $SQL;
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
    
}