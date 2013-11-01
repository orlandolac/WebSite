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
class Default_Model_Tabela_Projeto extends Zend_Db_Table_Abstract {

    public $_db;
    protected $_name = 'pro_projeto';
    protected $_primary = array('projeto_id');
    protected $dialogo = null;
    protected $data = array(
        'get'                   => array(),
        'pagina'                => array(
            'atual'             => 1,
            'max-itens'         => 20,
            'qtd-itens'         => 0,
            'total-itens'       => 0,
            'fim'               => 1,
            'ordem'             => array('P.projeto_data_apresentacao DESC')
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
        if(isset($get['palavra-chave']) && strlen($get['palavra-chave']) > 0){
            $where[] = 'P.projeto_nome like ' . $this->getDefaultAdapter()->quoteInto('?', '%' . $get['palavra-chave'] . '%');
        }
        
        if(isset($get['ano']) && $get['ano'] >= 1988 && $get['ano'] <= date('Y')){
            $where[] = 'P.projeto_ano=' . $this->getDefaultAdapter()->quoteInto('?', $get['ano']);
        }
        
        if(isset($get['tipo']) && $get['tipo'] > 0){
            $where[] = 'P.projeto_tplei_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['tipo']);
        }
        
        if(isset($get['situacao']) && $get['situacao'] > 0){
            $where[] = 'P.projeto_situacao_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['situacao']);
        }
        
        $tabela = 'pro_projeto P';
        if(isset($get['pessoa']) && is_numeric($get['pessoa']) && $get['pessoa'] > 0 && $get['pessoa'] % 1 == 0){
            $tabela = 'pro_projeto P, pro_projeto_x_pessoa PP';
            $where[] = 'PP.pessoa_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['pessoa']);
            $where[] = 'PP.projeto_id=P.projeto_id';
        }elseif(isset($get['partido']) && is_numeric($get['partido']) && $get['partido'] > 0 && $get['partido'] % 1 == 0) {
            $tabela = 'pro_projeto P, pro_projeto_x_partido PP';
            $where[] = 'PP.partido_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['partido']);
            $where[] = 'PP.projeto_id=P.projeto_id';
        }elseif(isset($get['usuario']) && is_numeric($get['usuario']) && $get['usuario'] > 0 && $get['usuario'] % 1 == 0){
            $tabela = 'pro_projeto P, pro_projeto_x_usuario PU';
            $where[] = 'PU.user_id=' . $this->getDefaultAdapter()->quoteInto('?', $get['usuario']);
            $where[] = 'PU.projeto_id=P.projeto_id';
        }
        
        if(isset($this->data['get']['votados'])){
        }else{
            if(Zend_Auth::getInstance()->hasIdentity()){
                if(isset($get['controller']) && $get['controller'] == 'projetos' && $get['action'] == 'index'){
                    $this->data['get']['votados'] = array();
                    foreach ($this->_db->fetchAll('SELECT * FROM pro_projeto_x_usuario WHERE user_id=' . Zend_Auth::getInstance()->getIdentity()->user_id) as $value) {
                        $this->data['get']['votados'][] = $value['projeto_id'];
                    }
                    $this->data['get']['votados'] = implode(',', $this->data['get']['votados']);
                }else{
                    $this->data['get']['votados'] = '';
                }
            }else{
                $this->data['get']['votados'] = '';
            }
        }
        if($this->data['get']['votados'] != ''){
            $where[] = 'P.projeto_id NOT IN (' . $this->data['get']['votados'] . ')';
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
            if($SQL == ''){
                $SQL = ' WHERE ';
            }else{
                $SQL .= ' && ';
            }
            $SQL = 'SELECT * FROM ' . $tabela . ', pro_situacao S, pro_tipo_lei T ' . $SQL . ' P.projeto_situacao_id=S.situacao_id && P.projeto_tplei_id=T.tplei_id';
            
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
        return $this->_db->fetchRow('
            SELECT *
            FROM pro_projeto P, pro_situacao S, pro_tipo_lei T, pro_apreciacao A, pro_regime R
            WHERE P.projeto_id=' . $this->_db->quoteInto('?', $id) . '
               && P.projeto_situacao_id=S.situacao_id
               && P.projeto_tplei_id=T.tplei_id
               && P.projeto_apreciacao_id=A.apreciacao_id
               && P.projeto_regime_id=R.regime_id
            LIMIT 0, 1
        ');
    }
    
    public function detalharUrl($id){
        return $this->_db->fetchRow('
            SELECT *
            FROM pro_projeto P, pro_situacao S, pro_tipo_lei T, pro_apreciacao A, pro_regime R
            WHERE P.projeto_url=' . $this->_db->quoteInto('?', $id) . '
               && P.projeto_situacao_id=S.situacao_id
               && P.projeto_tplei_id=T.tplei_id
               && P.projeto_apreciacao_id=A.apreciacao_id
               && P.projeto_regime_id=R.regime_id
            LIMIT 0, 1
        ');
    }
    
    public static function calculaPontuacao($projeto_id) {
        $data = self::$_defaultDb->fetchRow('SELECT projeto_id, projeto_ano, projeto_ano_votacao, projeto_qtd_positivos, projeto_qtd_negativos, projeto_qtd_positivos+projeto_qtd_negativos AS projeto_qtd_total FROM pro_projeto WHERE projeto_id=' . $projeto_id);
        if(isset($data['projeto_id']) && $data['projeto_qtd_total']>0){
            
            // CALCULA A NOTA //
            $SIM = ($data['projeto_qtd_positivos']>0)?((($data['projeto_qtd_positivos']*100)/$data['projeto_qtd_total'])):(0);
            $NAO = ($data['projeto_qtd_negativos']>0)?((($data['projeto_qtd_negativos']*100)/$data['projeto_qtd_total'])):(0);
            if($SIM >= $NAO){
                $CONTRARIO = 2;
                $NOTA = $SIM;
                if(($SIM - $NAO) < 10){
                    $NOTA *= 0.7;
                }
            }else{
                $CONTRARIO = 1;
                $NOTA = $NAO;
                if(($NAO - $SIM) < 10){
                    $NOTA *= 0.7;
                }
            }
            
            // CALCULA O PESO //
            $PESO = self::$_defaultDb->fetchRow('SELECT * FROM pro_projeto_x_tema PT, pro_tema T WHERE projeto_id=' . $data['projeto_id'] . ' && PT.tema_id=T.tema_id ORDER BY T.tema_peso DESC LIMIT 0,1');
            if($PESO){
                $PESO = $PESO['tema_peso'];
            }else{
                $PESO = 1;
            }
            
            ########################################################################################
            
            // CALCULA BP (BASE DA PONTUAÇÃO) //
            $BP = $NOTA * $PESO;
            
            ########################################################################################
            
            // QUEM CRIOU O PROJETO E VOTOU SIM
            $criouVotouSim = $BP*2*(($NAO>$SIM)?(-1):(1));
            
            // QUEM CRIOU O PROJETO E VOTOU NAO
            $criouVotouNao = $BP*2*(($NAO>$SIM)?(-0.5):(-1));
            
            // QUEM CRIOU O PROJETO E VOTOU NULO
            $criouVotouNulo = $criouVotouSim;
            
            // QUEM CRIOU O PROJETO E ESTA SEM VOTO
            $criouSemVoto = $criouVotouSim;
            
            // QUEM SÓ VOTOU SIM NO PROJETO
            $votouSim = $BP*(($NAO>$SIM)?(-1):(1));
            
            // QUEM SÓ VOTOU NÃO NO PROJETO
            $votouNao = $BP*(($NAO>$SIM)?(1):(-1));
            
            // QUEM SÓ VOTOU NULO NO PROJETO
            $votouNulo = ((false)?($votouNao):(0)); // se a populacao for contrariada quem votou nulo recebe o mesmo de quem contrariou.
            
            // QUEM SÓ ESTA SEM VOTO
            $semVoto = 0;
            
            ########################################################################################
            
            $res = self::$_defaultDb->fetchAll('SELECT pessoa_id, pessoa_criacao_id, pessoa_participacao_id FROM pro_projeto_x_pessoa WHERE projeto_id=' . $data['projeto_id']);
            if($res){
                foreach ($res as $value) {
                    if($value['pessoa_criacao_id'] == 1){
                        $pnts = $criouSemVoto;
                        switch ($value['pessoa_participacao_id']) {
                            case 1: $pnts = $criouVotouSim; break;
                            case 2: $pnts = $criouVotouNao; break;
                            case 3: $pnts = $criouVotouNulo; break;
                        }
                    }else{
                        $pnts = $semVoto;
                        switch ($value['pessoa_participacao_id']) {
                            case 1: $pnts = $votouSim; break;
                            case 2: $pnts = $votouNao; break;
                            case 3: $pnts = $votouNulo; break;
                        }
                    }
                    self::$_defaultDb->update('pro_projeto_x_pessoa', array(
                        'pessoa_pontuacao' => $pnts
                    ), 'projeto_id=' . $data['projeto_id'] . ' && pessoa_id=' . $value['pessoa_id']);
                    
                    $ups = array();
                    if($data['projeto_ano_votacao'] > 0 && $data['projeto_ano'] != $data['projeto_ano_votacao']){
                        $tmp = self::$_defaultDb->fetchRow('
                            SELECT sum(PP.pessoa_pontuacao) AS qtd, sum(P.projeto_qtd_positivos) AS sim, sum(P.projeto_qtd_negativos) AS nao
                            FROM pro_projeto_x_pessoa PP, pro_projeto P
                            WHERE PP.pessoa_id=' . $value['pessoa_id'] . '
                               && ( P.projeto_ano=' . $data['projeto_ano'] . ' || P.projeto_ano_votacao=' . $data['projeto_ano'] . ' )
                               && PP.projeto_id=P.projeto_id
                        ');
                        if($tmp){
                            $ups[] = array('ano' => $data['projeto_ano'], 'qtd' => $tmp['qtd'], 'sim' => $tmp['sim'], 'nao' => $tmp['nao']);
                        }else{
                            $ups[] = array('ano' => $data['projeto_ano'], 'qtd' => 0, 'sim' => 0, 'nao' => 0);
                        }
                        $tmp = self::$_defaultDb->fetchRow('
                            SELECT sum(PP.pessoa_pontuacao) AS qtd, sum(P.projeto_qtd_positivos) AS sim, sum(P.projeto_qtd_negativos) AS nao
                            FROM pro_projeto_x_pessoa PP, pro_projeto P
                            WHERE PP.pessoa_id=' . $value['pessoa_id'] . '
                               && ( P.projeto_ano=' . $data['projeto_ano_votacao'] . ' || P.projeto_ano_votacao=' . $data['projeto_ano_votacao'] . ' )
                               && PP.projeto_id=P.projeto_id
                        ');
                        if($tmp){
                            $ups[] = array('ano' => $data['projeto_ano_votacao'], 'qtd' => $tmp['qtd'], 'sim' => $tmp['sim'], 'nao' => $tmp['nao']);
                        }else{
                            $ups[] = array('ano' => $data['projeto_ano_votacao'], 'qtd' => 0, 'sim' => 0, 'nao' => 0);
                        }
                    }else{
                        $tmp = self::$_defaultDb->fetchRow('
                            SELECT sum(PP.pessoa_pontuacao) AS qtd, sum(P.projeto_qtd_positivos) AS sim, sum(P.projeto_qtd_negativos) AS nao
                            FROM pro_projeto_x_pessoa PP, pro_projeto P
                            WHERE PP.pessoa_id=' . $value['pessoa_id'] . '
                               && P.projeto_ano=' . $data['projeto_ano'] . '
                               && PP.projeto_id=P.projeto_id
                        ');
                        if($tmp){
                            $ups[] = array('ano' => $data['projeto_ano'], 'qtd' => $tmp['qtd'], 'sim' => $tmp['sim'], 'nao' => $tmp['nao']);
                        }else{
                            $ups[] = array('ano' => $data['projeto_ano'], 'qtd' => 0, 'sim' => 0, 'nao' => 0);
                        }
                    }
                    
                    foreach ($ups as $up) {
                        $tmp = self::$_defaultDb->update('pu_pessoa_x_ranking', array(
                            'rank_qtd_pontos'   => $up['qtd'],
                            'rank_qtd_positivos'=> $up['sim'],
                            'rank_qtd_negativos'=> $up['nao']
                        ), 'rank_ano=' . $up['ano'] . ' && pessoa_id=' . $value['pessoa_id']);
                        if($tmp){
                        }else{
                            try {
                                self::$_defaultDb->insert('pu_pessoa_x_ranking', array(
                                    'rank_ano'          => $up['ano'],
                                    'pessoa_id'         => $value['pessoa_id'],
                                    'rank_qtd_pontos'   => $up['qtd'],
                                    'rank_qtd_positivos'=> $up['sim'],
                                    'rank_qtd_negativos'=> $up['nao']
                                ));
                            } catch (Exception $exc) {
                            }
                        }
                    }
                }
            }
            
            ########################################################################################
            
            $res = self::$_defaultDb->fetchAll('SELECT partido_id, partido_criacao_id, partido_participacao_id FROM pro_projeto_x_partido WHERE projeto_id=' . $data['projeto_id']);
            if($res){
                foreach ($res as $value) {
                    if($value['partido_criacao_id'] == 1){
                        $pnts = $criouSemVoto;
                        switch ($value['partido_participacao_id']) {
                            case 1: $pnts = $criouVotouSim; break;
                            case 2: $pnts = $criouVotouNao; break;
                            case 3: $pnts = $criouVotouNulo; break;
                        }
                    }else{
                        $pnts = $semVoto;
                        switch ($value['partido_participacao_id']) {
                            case 1: $pnts = $votouSim; break;
                            case 2: $pnts = $votouNao; break;
                            case 3: $pnts = $votouNulo; break;
                        }
                    }
                    self::$_defaultDb->update('pro_projeto_x_partido', array(
                        'partido_pontuacao' => $pnts
                    ), 'projeto_id=' . $data['projeto_id'] . ' && partido_id=' . $value['partido_id']);
                    
                    $ups = array();
                    if($data['projeto_ano_votacao'] > 0 && $data['projeto_ano'] != $data['projeto_ano_votacao']){
                        $tmp = self::$_defaultDb->fetchRow('
                            SELECT sum(PP.partido_pontuacao) AS qtd, sum(P.projeto_qtd_positivos) AS sim, sum(P.projeto_qtd_negativos) AS nao
                            FROM pro_projeto_x_partido PP, pro_projeto P
                            WHERE PP.partido_id=' . $value['partido_id'] . '
                               && ( P.projeto_ano=' . $data['projeto_ano'] . ' || P.projeto_ano_votacao=' . $data['projeto_ano'] . ' )
                               && PP.projeto_id=P.projeto_id
                        ');
                        if($tmp){
                            $ups[] = array('ano' => $data['projeto_ano'], 'qtd' => $tmp['qtd'], 'sim' => $tmp['sim'], 'nao' => $tmp['nao']);
                        }else{
                            $ups[] = array('ano' => $data['projeto_ano'], 'qtd' => 0, 'sim' => 0, 'nao' => 0);
                        }
                        $tmp = self::$_defaultDb->fetchRow('
                            SELECT sum(PP.partido_pontuacao) AS qtd, sum(P.projeto_qtd_positivos) AS sim, sum(P.projeto_qtd_negativos) AS nao
                            FROM pro_projeto_x_partido PP, pro_projeto P
                            WHERE PP.partido_id=' . $value['partido_id'] . '
                               && ( P.projeto_ano=' . $data['projeto_ano_votacao'] . ' || P.projeto_ano_votacao=' . $data['projeto_ano_votacao'] . ' )
                               && PP.projeto_id=P.projeto_id
                        ');
                        if($tmp){
                            $ups[] = array('ano' => $data['projeto_ano_votacao'], 'qtd' => $tmp['qtd'], 'sim' => $tmp['sim'], 'nao' => $tmp['nao']);
                        }else{
                            $ups[] = array('ano' => $data['projeto_ano_votacao'], 'qtd' => 0, 'sim' => 0, 'nao' => 0);
                        }
                    }else{
                        $tmp = self::$_defaultDb->fetchRow('
                            SELECT sum(PP.partido_pontuacao) AS qtd, sum(P.projeto_qtd_positivos) AS sim, sum(P.projeto_qtd_negativos) AS nao
                            FROM pro_projeto_x_partido PP, pro_projeto P
                            WHERE PP.partido_id=' . $value['partido_id'] . '
                               && P.projeto_ano=' . $data['projeto_ano'] . '
                               && PP.projeto_id=P.projeto_id
                        ');
                        if($tmp){
                            $ups[] = array('ano' => $data['projeto_ano'], 'qtd' => $tmp['qtd'], 'sim' => $tmp['sim'], 'nao' => $tmp['nao']);
                        }else{
                            $ups[] = array('ano' => $data['projeto_ano'], 'qtd' => 0, 'sim' => 0, 'nao' => 0);
                        }
                    }
                    
                    foreach ($ups as $up) {
                        $tmp = self::$_defaultDb->update('pu_partido_x_ranking', array(
                            'rank_qtd_pontos'   => $up['qtd'],
                            'rank_qtd_positivos'=> $up['sim'],
                            'rank_qtd_negativos'=> $up['nao']
                        ), 'rank_ano=' . $up['ano'] . ' && partido_id=' . $value['partido_id']);
                        if($tmp){
                        }else{
                            try {
                                self::$_defaultDb->insert('pu_partido_x_ranking', array(
                                    'rank_ano'          => $up['ano'],
                                    'partido_id'         => $value['partido_id'],
                                    'rank_qtd_pontos'   => $up['qtd'],
                                    'rank_qtd_positivos'=> $up['sim'],
                                    'rank_qtd_negativos'=> $up['nao']
                                ));
                            } catch (Exception $exc) {
                            }
                        }
                    }
                }
            }
            
            ########################################################################################
            
//            self::$_defaultDb->update('pro_projeto_x_pessoa', array(
//                'pessoa_pontuacao' => $criouVotouSim
//            ), 'projeto_id=' . $data['projeto_id'] . ' && pessoa_criacao_id=1 && pessoa_participacao_id=1');
//            
//            self::$_defaultDb->update('pro_projeto_x_pessoa', array(
//                'pessoa_pontuacao' => $criouVotouNao
//            ), 'projeto_id=' . $data['projeto_id'] . ' && pessoa_criacao_id=1 && pessoa_participacao_id=2');
//            
//            self::$_defaultDb->update('pro_projeto_x_pessoa', array(
//                'pessoa_pontuacao' => $criouVotouNulo
//            ), 'projeto_id=' . $data['projeto_id'] . ' && pessoa_criacao_id=1 && pessoa_participacao_id=3');
//            
//            self::$_defaultDb->update('pro_projeto_x_pessoa', array(
//                'pessoa_pontuacao' => $criouSemVoto
//            ), 'projeto_id=' . $data['projeto_id'] . ' && pessoa_criacao_id=1 && pessoa_participacao_id=0');
//            
//            self::$_defaultDb->update('pro_projeto_x_pessoa', array(
//                'pessoa_pontuacao' => $votouSim
//            ), 'projeto_id=' . $data['projeto_id'] . ' && pessoa_criacao_id=0 && pessoa_participacao_id=1');
//            
//            self::$_defaultDb->update('pro_projeto_x_pessoa', array(
//                'pessoa_pontuacao' => $votouNao
//            ), 'projeto_id=' . $data['projeto_id'] . ' && pessoa_criacao_id=0 && pessoa_participacao_id=2');
//            
//            self::$_defaultDb->update('pro_projeto_x_pessoa', array(
//                'pessoa_pontuacao' => $votouNulo
//            ), 'projeto_id=' . $data['projeto_id'] . ' && pessoa_criacao_id=0 && pessoa_participacao_id=3');
//            
//            self::$_defaultDb->update('pro_projeto_x_pessoa', array(
//                'pessoa_pontuacao' => $semVoto
//            ), 'projeto_id=' . $data['projeto_id'] . ' && pessoa_criacao_id=0 && pessoa_participacao_id=0');
//            
//            ########################################################################################
//            
//            self::$_defaultDb->update('pro_projeto_x_partido', array(
//                'partido_pontuacao' => $criouVotouSim
//            ), 'projeto_id=' . $data['projeto_id'] . ' && partido_criacao_id=1 && partido_participacao_id=1');
//            
//            self::$_defaultDb->update('pro_projeto_x_partido', array(
//                'partido_pontuacao' => $criouVotouNao
//            ), 'projeto_id=' . $data['projeto_id'] . ' && partido_criacao_id=1 && partido_participacao_id=2');
//            
//            self::$_defaultDb->update('pro_projeto_x_partido', array(
//                'partido_pontuacao' => $criouVotouNulo
//            ), 'projeto_id=' . $data['projeto_id'] . ' && partido_criacao_id=1 && partido_participacao_id=3');
//            
//            self::$_defaultDb->update('pro_projeto_x_partido', array(
//                'partido_pontuacao' => $criouSemVoto
//            ), 'projeto_id=' . $data['projeto_id'] . ' && partido_criacao_id=1 && partido_participacao_id=0');
//            
//            self::$_defaultDb->update('pro_projeto_x_partido', array(
//                'partido_pontuacao' => $votouSim
//            ), 'projeto_id=' . $data['projeto_id'] . ' && partido_criacao_id=0 && partido_participacao_id=1');
//            
//            self::$_defaultDb->update('pro_projeto_x_partido', array(
//                'partido_pontuacao' => $votouNao
//            ), 'projeto_id=' . $data['projeto_id'] . ' && partido_criacao_id=0 && partido_participacao_id=2');
//            
//            self::$_defaultDb->update('pro_projeto_x_partido', array(
//                'partido_pontuacao' => $votouNulo
//            ), 'projeto_id=' . $data['projeto_id'] . ' && partido_criacao_id=0 && partido_participacao_id=3');
//            
//            self::$_defaultDb->update('pro_projeto_x_partido', array(
//                'partido_pontuacao' => $semVoto
//            ), 'projeto_id=' . $data['projeto_id'] . ' && partido_criacao_id=0 && partido_participacao_id=0');
            
            ########################################################################################
            
        }
    }
}



//                    if($value['pessoa_criacao_id'] == 1){
////                        if($value['pessoa_ano'] == $value['pessoa_ano_votacao']){
////                        }else{
////                        }
//                    }else{
////                        if($value['pessoa_ano_votacao'] > 0){
////                            $res = self::$_defaultDb->fetchAll('
////                                SELECT PP.pessoa_id, P.projeto_ano, sum(PP.pessoa_pontuacao), sum(P.projeto_qtd_positivos), sum(P.projeto_qtd_negativos)
////                                FROM pro_projeto_x_pessoa PP, pro_projeto P
////                                WHERE PP.pessoa_id IN (' . implode(', ', $tmp) . ')
////                                   && PP.projeto_id=P.projeto_id
////                                GROUP BY PP.pessoa_id, P.projeto_ano
////                            ');
////                        }
////                        if($res){
////                            foreach ($res as $value) {
////                                $tmp = self::$_defaultDb->update('pu_pessoa_x_ranking', $value, 'rank_ano=' . $value['rank_ano'] . ' && pessoa_id=' . $value['pessoa_id']);
////                                if($tmp){
////                                }else{
////                                    try {
////                                        self::$_defaultDb->insert('pu_pessoa_x_ranking', $value);
////                                    } catch (Exception $exc) {
////                                    }
////                                }
////                            }
////                        }
//                    }


            
//            // ATUALIZA RANKING DOS POLITICOS //
//            $tmp = array();
//            foreach (self::$_defaultDb->fetchAll('SELECT pessoa_id FROM pro_projeto_x_pessoa WHERE projeto_id=' . $data['projeto_id']) as $value) {
//                $tmp[] = $value['pessoa_id'];
//            }
//            if(count($tmp) > 0){
////                $res = self::$_defaultDb->fetchAll('
////                    SELECT PP.pessoa_id, P.projeto_ano AS rank_ano, sum(PP.pessoa_pontuacao) AS rank_qtd_pontos, sum(P.projeto_qtd_positivos) AS rank_qtd_positivos, sum(P.projeto_qtd_negativos) AS rank_qtd_negativos
////                    FROM pro_projeto_x_pessoa PP, pro_projeto P
////                    WHERE PP.pessoa_id IN (' . implode(', ', $tmp) . ')
////                       && PP.projeto_id=P.projeto_id
////                    GROUP BY PP.pessoa_id, P.projeto_ano
////                ');
////                if($res){
////                    foreach ($res as $value) {
////                        $tmp = self::$_defaultDb->update('pu_pessoa_x_ranking', $value, 'rank_ano=' . $value['rank_ano'] . ' && pessoa_id=' . $value['pessoa_id']);
////                        if($tmp){
////                        }else{
////                            try {
////                                self::$_defaultDb->insert('pu_pessoa_x_ranking', $value);
////                            } catch (Exception $exc) {
////                            }
////                        }
////                    }
////                }
//            }
//            
////            // ATUALIZA RANKING DOS PARTIDOS //
////            $tmp = array();
////            foreach (self::$_defaultDb->fetchAll('SELECT partido_id FROM pro_projeto_x_partido WHERE projeto_id=' . $data['projeto_id']) as $value) {
////                $tmp[] = $value['partido_id'];
////            }
////            if(count($tmp) > 0){
//////                $res = self::$_defaultDb->fetchAll('
//////                    SELECT PP.partido_id, P.projeto_ano AS rank_ano, sum(PP.partido_pontuacao) AS rank_qtd_pontos, sum(P.projeto_qtd_positivos) AS rank_qtd_positivos, sum(P.projeto_qtd_negativos) AS rank_qtd_negativos
//////                    FROM pro_projeto_x_pessoa PP, pro_projeto P
//////                    WHERE PP.partido_id IN (' . implode(', ', $tmp) . ')
//////                       && PP.projeto_id=P.projeto_id
//////                    GROUP BY PP.partido_id, P.projeto_ano
//////                ');
//////                if($res){
//////                    foreach ($res as $value) {
//////                        $tmp = self::$_defaultDb->update('pu_partido_x_ranking', $value, 'rank_ano=' . $value['rank_ano'] . ' && partido_id=' . $value['partido_id']);
//////                        if($tmp){
//////                        }else{
//////                            try {
//////                                self::$_defaultDb->insert('pu_partido_x_ranking', $value);
//////                            } catch (Exception $exc) {
//////                            }
//////                        }
//////                    }
//////                }
////            }
//            
//            
//            
//            
            
