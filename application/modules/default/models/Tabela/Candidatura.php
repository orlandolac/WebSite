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
class Default_Model_Tabela_Candidatura extends Zend_Db_Table_Abstract {

    public $_db;
    protected $_name = 'pu_candidatura';
    protected $_primary = array('cand_id');
    protected $dialogo = null;
    protected $data = array(
        'get'                   => array(),
        'pagina'                => array(
            'atual'             => 1,
            'max-itens'         => 18,
            'qtd-itens'         => 0,
            'total-itens'       => 0,
            'fim'               => 1,
            'ordem'             => array('C.cand_cargo_id', 'C.cand_urna_nome') // 'qtd DESC', 'C.cand_status_cand', 'C.cand_status_total',
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

    public function buscar(array $get, $cargo_id) {
        $this->data['get'] = $get;        
        
        // WHERE //
        $where = array();
        if(isset($get['eleicao']) && $get['eleicao'] > 0 && Default_Model_Lista_Eleicao::get($get['eleicao'])){
            $where[] = 'C.cand_eleicao_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['eleicao']);
        }
        
        // LOCALIDADE //
        $sys = new Zend_Session_Namespace('sistem');
        if($sys->localidade->estado_id == 8){ // DF //
            if($cargo_id == 7 || $cargo_id > 10){
                $this->dialogo->add('Alerta', $sys->localidade->estado_nome . ' <b>não elege</b> Deputados Estaduais, Prefeitos ou Vereadores.');
                return $this->data;
            }
        }else{
            if($cargo_id == 8){
                $this->dialogo->add('Alerta', $sys->localidade->estado_nome . ' <b>não elege</b> Deputados Distritais.');
                return $this->data;
            }
        }
        if($cargo_id > 2){
            $where[] = 'C.cand_estado_id=' . $this->getDefaultAdapter()->quoteInto('?', $sys->localidade->estado_id);
        }else{
            $where[] = 'C.cand_estado_id=6';
        }
        if($cargo_id > 10){
            $where[] = 'C.cand_municipio_id=' . $this->getDefaultAdapter()->quoteInto('?', $sys->localidade->municipio_id);
        }else{
            $where[] = 'C.cand_municipio_id=0';
        }
        
        // CARGO //
        $cargoListaId[] = $cargo_id;
        switch ($cargo_id) {
            case  1: $cargoListaId[] =  2; break;
            case  3: $cargoListaId[] =  4; break;
            case  5: $cargoListaId[] =  9; $cargoListaId[] = 10; break;
            case 11: $cargoListaId[] = 12; break;
        }
        $where[] = 'C.cand_cargo_id IN (' . implode(',', $cargoListaId) . ')';
        
        // PARTIDO //
        if(isset($get['partido']) && $get['partido'] > 0){
            $where[] = 'C.cand_partido_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['partido']);
        }
        
        // PALAVRA-CHAVE //
        if(isset($get['palavra-chave'])){
            $where[] = 'C.cand_urna_nome like ' . $this->getDefaultAdapter()->quoteInto('?', '%' . $get['palavra-chave'] . '%');
        }
        
        $SQL = '';
        if(count($where) > 0){
            $SQL = ' WHERE ' . implode(' && ', $where);
        }
        //$SQL .= ' GROUP BY C.cand_pessoa_id';
        //$tmp = $this->_db->fetchRow('SELECT count(*) AS qtd FROM pu_candidatura C ' . $SQL);
        
        $tmp = $this->_db->fetchRow('SELECT count(DISTINCT C.cand_pessoa_id) AS qtd FROM pu_candidatura C ' . $SQL);
        if ($tmp['qtd'] > 0) {
            // PAGINA //
            if(isset($get['pagina']) && $get['pagina'] > 0){
                $this->data['pagina']['atual'] = $get['pagina'];
            }
            
            // QTD & LIMITE //
            $this->data['pagina']['total-itens'] = $tmp['qtd'];
            $this->data['pagina']['fim'] = ceil($this->data['pagina']['total-itens'] / $this->data['pagina']['max-itens']);
        
            // BUSCA //
            $SQL = 'SELECT C.*, count(*) AS qtd FROM pu_candidatura C ' . $SQL . ' GROUP BY C.cand_pessoa_id ';
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
        return $this->fetchRow($this->_db->quoteInto('cand_id=?', $id));
    }
    
}