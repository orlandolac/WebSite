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
class Default_Model_Tabela_Noticia extends Zend_Db_Table_Abstract {

    public $_db;
    protected $_name = 'not_noticia';
    protected $_primary = array('noticia_id');
    protected $dialogo = null;
    protected $data = array(
        'get'                   => array(),
        'pagina'                => array(
            'atual'             => 1,
            'max-itens'         => 20,
            'qtd-itens'         => 0,
            'total-itens'       => 0,
            'fim'               => 1,
            'ordem'             => array('N.noticia_data DESC')
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
        
        // ORDER //
        if(isset($this->data['get']['ordem'])){
            if($this->data['get']['ordem'] == 'importancia'){
                $this->data['pagina']['ordem'] = array('N.noticia_importancia_media DESC', 'N.noticia_data DESC');
            }else{
                unset($this->data['get']['ordem']);
            }
        }
        
        // WHERE //
        $where = array();
        if(isset($get['usuario']) && is_numeric($get['usuario']) && $get['usuario'] > 0 && $get['usuario'] % 1 == 0){
            $where[] = 'N.noticia_user_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['usuario']);
        }
        
        if(isset($get['palavra-chave']) && strlen($get['palavra-chave']) > 0){
            $where[] = 'N.noticia_titulo like ' . $this->getDefaultAdapter()->quoteInto('?', '%' . $get['palavra-chave'] . '%');
        }
        
        if(isset($get['veiculo']) && is_numeric($get['veiculo']) && $get['veiculo'] > 0 && $get['veiculo'] % 1 == 0){
            $where[] = 'N.noticia_veiculo_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['veiculo']);
        }
        
        $tabela = 'not_noticia N';
        if(isset($get['pessoa']) && is_numeric($get['pessoa']) && $get['pessoa'] > 0 && $get['pessoa'] % 1 == 0){
            $tabela = 'not_noticia N, not_noticia_x_pessoa NP';
            $where[] = 'NP.pessoa_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['pessoa']);
            $where[] = 'NP.noticia_id=N.noticia_id';
        }elseif (isset($get['partido']) && is_numeric($get['partido']) && $get['partido'] > 0 && $get['partido'] % 1 == 0) {
            $tabela = 'not_noticia N, not_noticia_x_partido NP';
            $where[] = 'NP.partido_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['partido']);
            $where[] = 'NP.noticia_id=N.noticia_id';
        }
        
        $SQL = '';
        if(count($where) > 0){
            $SQL = ' WHERE ' . implode(' && ', $where);
        }
        
        $tmp = $this->_db->fetchRow('SELECT count(*) AS qtd FROM ' . $tabela . ' ' . $SQL);
        if ($tmp['qtd'] > 0) {
            // PAGINA //
            if(isset($get['pagina']) && $get['pagina'] > 0){
                $this->data['pagina']['atual'] = $get['pagina'];
            }

            // QTD & LIMITE //
            $this->data['pagina']['total-itens'] = $tmp['qtd'];
            $this->data['pagina']['fim'] = ceil($this->data['pagina']['total-itens'] / $this->data['pagina']['max-itens']);

            // BUSCA //
            $SQL = 'SELECT * FROM ' . $tabela . ' ' . $SQL;
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
        return $this->_db->fetchRow('SELECT * FROM not_noticia N, not_veiculo V WHERE ' . $this->_db->quoteInto('N.noticia_id=?', $id) . ' && N.noticia_veiculo_id=V.veiculo_id');
    }
    
    public function cadastrar($data) {
        if(($data = $this->eValido($data))){
            $tmp = $this->_db->fetchRow('SELECT * FROM not_veiculo WHERE veiculo_link=' . $this->_db->quoteInto('?', $data['noticia_dominio']) . ' LIMIT 0, 1');
            if($tmp){
                if($tmp['veiculo_status_id'] != 0){
                    $this->dialogo->add('Erro', 'O veículo desta notícia perdeu o direito de ter suas notícias postadas no OPovoUnido.com, por tempo indeterminado.');
                    return;
                }else{
                    $data['veiculo_id'] = $tmp['veiculo_id'];
                }
            }else{
                $tmp = new Zend_Db_Table(array('name' => 'not_veiculo'));
                try {
                    $data['veiculo_id'] = $tmp->insert(array(
                        'veiculo_nome' => $data['noticia_dominio'],
                        'veiculo_link' => $data['noticia_dominio']
                    ));
                    $this->dialogo->add('Ok', 'Um novo veículo foi registrado. Obrigado!');
                } catch (Exception $exc) {
                    $this->dialogo->add('Erro', 'Não foi possível registrar o novo veículo, tente novamente mais tarde.');
                }
            }
            if(!(isset($data['veiculo_id']) && $data['veiculo_id'] > 0)){
                $this->dialogo->add('Erro', 'Não foi possível identificar o veículo desta notícia.');
            }else{
                $tmp = $this->fetchRow('noticia_link=' . $this->_db->quoteInto('?', $data['noticia_link']));
                if($tmp){
                    $data['noticia_id'] = $tmp['noticia_id'];
                    $this->duplicado($data);
                }else{
                    $this->novo($data);
                }
                return true;
            }
        }
        $this->dialogo->add('Alerta', 'Para voltar ao cadastrar da notícia <b><a onclick="ligaDialogo();">clique aqui</a></b>.');
        return false;
    }
    
    private function eValido($data) {
        if(!Zend_Auth::getInstance()->hasIdentity()){
            $this->dialogo->add('Alerta', 'Funcionalidade exclusiva para usuários cadastrados.');
            return false;
        }
        
        if(isset($data['noticia_data_dia']) && isset($data['noticia_data_mes']) && isset($data['noticia_data_ano']) && isset($data['noticia_link']) && isset($data['noticia_titulo']) && isset($data['noticia_descricao']) && isset($data['noticia_palavra_chave']) && isset($data['noticia_imagem']) && isset($data['noticia_temas']) && isset($data['noticia_pessoas']) && isset($data['noticia_partidos'])){
            $erro=0;
            
            // verifica data //
            if(!(checkdate($data['noticia_data_mes'], $data['noticia_data_dia'], $data['noticia_data_ano']))){
                $this->dialogo->add('Erro', 'Data inválida');
                $erro++;
            }else{
                $data['noticia_data'] = strtotime($data['noticia_data_dia'] . '-' . $data['noticia_data_mes'] . '-' . $data['noticia_data_ano']);
            }

            // verifica link //
            if(filter_var($data['noticia_link'], FILTER_VALIDATE_URL) === FALSE) {
                $this->dialogo->add('Erro', 'O link da página da notícia é inválido.');
                $erro++;
            }else{
                $data['noticia_dominio'] = Alex_Util::getDominio($data['noticia_link']);
                if(!(strlen($data['noticia_dominio']) > 0)){
                    $this->dialogo->add('Erro', 'O link da página da notícia é inválido.');
                    $erro++;
                }
            }

            // verifica titulo //
            $tmp = explode(' ', $data['noticia_titulo']);
            if(count($tmp) < 2){
                $this->dialogo->add('Erro', 'O título deve ter no mínimo duas palavras.');
                $erro++;
            }

            // verifica descricao //
            if(strlen($data['noticia_descricao']) > 0){
                $tmp = explode(' ', $data['noticia_descricao']);
                if(count($tmp) < 3){
                    $this->dialogo->add('Erro', 'O resumo deve ter no mínimo três palavras.');
                    $erro++;
                }
            }

            // verifica imagem //
            if(strlen($data['noticia_imagem'])){
                if(filter_var($data['noticia_imagem'], FILTER_VALIDATE_URL) === FALSE){
                    $this->dialogo->add('Erro', 'O link da imagem da notícia é inválido.');
                    $erro++;
                }
            }

            // verifica temas //
            if(!(is_array($data['noticia_temas']) && count($data['noticia_temas']) > 0 && count($data['noticia_temas']) < 4)){
                $this->dialogo->add('Erro', 'A notícia deve ser relacionada a pelo menos UM tema e no máximo TRÊS.');
                $erro++;
            }

            // verifica relações //
            if(!((is_array($data['noticia_pessoas']) && count($data['noticia_pessoas']) > 0) || (is_array($data['noticia_partidos']) && count($data['noticia_partidos']) > 0))){
                $this->dialogo->add('Erro', 'A notícia deve ser relacionada a pelo menos uma pessoa ou partido.');
                $erro++;
            }
        
            if($erro == 0){
                return $data;
            }
        }else{
            $this->dialogo->add('Erro', 'Requisição inválida');
        }
        return false;
    }
    
    private function duplicado($data) {
        $novos = 0;
        if(is_array($data['noticia_pessoas'])){
            foreach ($data['noticia_pessoas'] as $value) {
                try {
                    $this->_db->insert('not_noticia_x_pessoa', array('pessoa_id' => $value, 'noticia_id' => $data['noticia_id']));
                    $novos++;
                } catch (Exception $exc) {
                }
            }
        }
        if(is_array($data['noticia_partidos'])){
            foreach ($data['noticia_partidos'] as $value) {
                try {
                    $this->_db->insert('not_noticia_x_partido', array('partido_id' => $value, 'noticia_id' => $data['noticia_id']));
                    $novos++;
                } catch (Exception $exc) {
                }
            }
        }
        foreach ($data['noticia_temas'] as $value) {
            try {
                $this->_db->insert('not_noticia_x_tema', array('noticia_id' => $data['noticia_id'], 'tema_id' => $value));
                $novos++;
            } catch (Exception $exc) {
            }
        }
        if($novos > 0){
            $backup = array(
                'noticia_id'        => $data['noticia_id'],
                'noticia_user_id'   => Zend_Auth::getInstance()->getIdentity()->user_id,
                'noticia_data'      => $data['noticia_data'],
                'noticia_titulo'    => $data['noticia_titulo'],
                'noticia_resumo'    => $data['noticia_descricao'],
                'noticia_imagem'    => $data['noticia_imagem'],
                'noticia_link'      => $data['noticia_link'],
                'noticia_temas'     => implode(';', $data['noticia_temas']),
                'noticia_pessoas'   => implode(';', $data['noticia_pessoas']),
                'noticia_partidos'  => implode(';', $data['noticia_partidos'])
            );
            try {
                $this->_db->insert('not_noticia_backup', $backup);
                $this->dialogo->add('Ok', 'Esta notícia já foi cadastrada anteriormente, mas sua requisição adicionou novas relações aos fatos. Obrigado!');
            } catch (Exception $exc) {
                $this->dialogo->add('Ok', 'Esta notícia já foi cadastrada anteriormente. Obrigado!');
            }
        }else{
            $this->dialogo->add('Ok', 'Esta notícia já foi cadastrada anteriormente. Obrigado!');
        }
    }
    
    private function novo($data) {
        $noticia_id = $this->insert(array(
            'noticia_user_id'           => Zend_Auth::getInstance()->getIdentity()->user_id,
            'noticia_veiculo_id'        => $data['veiculo_id'],
            'noticia_data'              => $data['noticia_data'],
            'noticia_titulo'            => $data['noticia_titulo'],
            'noticia_resumo'            => $data['noticia_descricao'],
            'noticia_imagem'            => $data['noticia_imagem'],
            'noticia_link'              => $data['noticia_link'],
            'noticia_data_ini'          => time(),
            'noticia_data_alt'          => time()
        ));
        
        if($noticia_id > 0){
            if(is_array($data['noticia_pessoas'])){
                foreach ($data['noticia_pessoas'] as $value) {
                    try {
                        $this->_db->insert('not_noticia_x_pessoa', array('pessoa_id' => $value, 'noticia_id' => $noticia_id));
                    } catch (Exception $exc) {
                    }
                }
            }
            if(is_array($data['noticia_partidos'])){
                foreach ($data['noticia_partidos'] as $value) {
                    try {
                        $this->_db->insert('not_noticia_x_partido', array('partido_id' => $value, 'noticia_id' => $noticia_id));
                    } catch (Exception $exc) {
                    }
                }
            }
            foreach ($data['noticia_temas'] as $value) {
                try {
                    $this->_db->insert('not_noticia_x_tema', array('noticia_id' => $noticia_id, 'tema_id' => $value));
                } catch (Exception $exc) {
                }
            }
            $backup = array(
                'noticia_id'        => $noticia_id,
                'noticia_user_id'   => Zend_Auth::getInstance()->getIdentity()->user_id,
                'noticia_data'      => $data['noticia_data'],
                'noticia_titulo'    => $data['noticia_titulo'],
                'noticia_resumo'    => $data['noticia_descricao'],
                'noticia_imagem'    => $data['noticia_imagem'],
                'noticia_link'      => $data['noticia_link'],
                'noticia_temas'     => implode(';', $data['noticia_temas']),
                'noticia_pessoas'   => implode(';', $data['noticia_pessoas']),
                'noticia_partidos'  => implode(';', $data['noticia_partidos'])
            );
            try {
                $this->_db->insert('not_noticia_backup', $backup);
            } catch (Exception $exc) {
            }
            $this->dialogo->add('Ok', 'A notícia foi cadastrada com sucesso. Obrigado!');
        }else{
            $this->dialogo->add('Erro', 'Erro ao tentar salvar a notícia, tente novamente mais tarde.');
        }
    }
    
}