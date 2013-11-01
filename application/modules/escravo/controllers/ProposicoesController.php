<?php
class Escravo_ProposicoesController extends Zend_Controller_Action {
    
    public function siglasAction() {
        $siglas = Camara_Proposicoes::listarSiglasTipoProposicao();
        $siglas = $siglas['sigla'];
        foreach ($siglas as $key => $value) {
            $tmp = (array) $value;
            $siglas[$key] = trim($tmp['@attributes']['tipoSigla']);
        }
        $siglas = array_unique($siglas);
        
        echo '<div style="font-size:30px">' . implode(' | ', $siglas) . '</div>';
    }

    public function cadastrarAction() {
        $tempo = time();
        $siglas = array('PEC', 'PLP', 'PL', 'PLC', 'PLN', 'PLS', 'MPV', 'PDC', 'PDN', 'PDS', 'PRC', 'PRN', 'PRF');

        // MOSTA LISTA DE EXECUÇÃO //
        $lista = array(0 => array('sigla'=> 0, 'ano'=> 0));
        for($ano=1988; $ano<=date('Y'); $ano++){
            foreach ($siglas as $sigla) {
                $lista[] = array('sigla'=> $sigla, 'ano'=> $ano);
            }
        }
        
        $this->inicilizaLoop();
        if(!isset($lista[$this->view->contador])){
            $this->finalizaLoop();
        }
        
        Alex_Util::debugEscreve('CONTADOR/QTD........: ' . $this->view->contador . '/' . count($lista));
        
        if($this->view->contador > 0){
            $qtdTotal = $qtdCadastro = 0;
            $sigla = $lista[$this->view->contador]['sigla'];
            $ano = $lista[$this->view->contador]['ano'];
            $lista = Camara_Proposicoes::listarProposicoes(array('sigla' => $sigla, 'ano' => $ano));
            $t = new Zend_Db_Table(array('name' => 'pro_projeto'));
            if(count($lista['proposicao']) > 0){
                foreach ($lista['proposicao'] as $item) {
                    $qtdTotal++;
                    $res = $t->fetchRow($t->getDefaultAdapter()->quoteInto('projeto_cd_origem=?', $item['id']));
                    if($res){
                        // nada a fazer //
                    }else{
                        $itemDetalhe = Camara_Proposicoes::obterProposicaoPorID($item['id']);
                        $projeto = array(
                          //'projeto_id'                => null,
                          //'projeto_status_id'         => 0,
                          //'projeto_origem_id'         => 0,
                            'projeto_cd_origem'         => trim($item['id']),
                            'projeto_url'               => mb_strtolower($sigla . '-' . $item['numero'] . '-' . $item['ano']),
                            'projeto_tplei_id'          => $this->sicTipoProposicao($item['tipoProposicao']),
                            'projeto_situacao_id'       => $this->sicSituacao($item['situacao']),
                            'projeto_regime_id'         => $this->sicRegime($item['regime']),
                            'projeto_apreciacao_id'     => $this->sicApreciacao($item['apreciacao']),
                            'projeto_numero'            => trim($item['numero']),
                            'projeto_ano'               => trim($item['ano']),
                            'projeto_nome'              => mb_strtoupper(trim($item['nome'])),
                            'projeto_ementa'            => trim(((is_string($item['txtEmenta']))?($item['txtEmenta']):(''))),
                            'projeto_ementa_explicada'  => trim(((is_string($item['txtExplicacaoEmenta']))?($item['txtExplicacaoEmenta']):(''))),
                            'projeto_autor'             => $this->trataAutor($item['autor1']),
                            'projeto_link_inteiro_teor' => trim(((is_string($itemDetalhe['LinkInteiroTeor']))?($itemDetalhe['LinkInteiroTeor']):(''))),
                            'projeto_data_apresentacao' => $this->sicDapaApresentacao($item['datApresentacao'])
                        );
                        
                        if(strlen($projeto['projeto_autor']) > 100){
                            $projeto['projeto_ementa_explicada'] .= '

AUTOR: ' . $projeto['projeto_autor'];
                            $projeto['projeto_autor'] = '';
                        }
                        
                        $projeto_id = 0;
                        try {
                            $projeto_id = $t->insert($projeto);
                            $qtdCadastro++;
                        } catch (Exception $exc) {
                            Alex_Util::debugEscreve('ERRO FATAL');
                            Alex_Util::debugEscreve($projeto);
                            Alex_Util::debugEscreve($item);
                            Alex_Util::debugEscreve($itemDetalhe);
                            echo '<pre>' . $exc->getTraceAsString() . '</pre>';
                            exit;
                        }
                        
                        // RELACIONAMENTOS //
                        if($projeto_id > 0){
                            // RELACIONA PESSOA ////////////////////////////////////////////////////
                            if(isset($item['autor1']['idecadastro']) && is_numeric($item['autor1']['idecadastro']) && $item['autor1']['idecadastro'] > 0){
                                $res = $t->getDefaultAdapter()->fetchRow('SELECT pessoa_id FROM pu_pessoa_de_para WHERE pessoa_cd_origem=' . $t->getDefaultAdapter()->quoteInto('?', $item['autor1']['idecadastro']));
                                if($res){
                                    if($res['pessoa_id'] > 0){
                                        try {
                                            $t->getDefaultAdapter()->insert('pro_projeto_x_pessoa', array(
                                                'projeto_id' => $projeto_id,
                                                'pessoa_id' => $res['pessoa_id'],
                                                'pessoa_criacao_id' => 1,
                                              //'pessoa_participacao_id' => '',
                                              //'pessoa_pontuacao' => ''
                                            ));
                                        } catch (Exception $exc) {
                                        }
                                    }
                                }else{
                                    try {
                                        $t->getDefaultAdapter()->insert('pu_pessoa_de_para', array(
                                          //'pessoa_id' => '',
                                          //'pessoa_origem_id' => '',
                                            'pessoa_cd_origem' => $item['autor1']['idecadastro'],
                                            'pessoa_nome' =>  ((is_string($item['autor1']['txtNomeAutor']))?($item['autor1']['txtNomeAutor']):('')) . ' - ' . ((is_string($item['autor1']['txtSiglaPartido']))?(trim($item['autor1']['txtSiglaPartido'])):('')) . ' - ' . ((is_string($item['autor1']['txtSiglaUF']))?($item['autor1']['txtSiglaUF']):(''))
                                        ));
                                    } catch (Exception $exc) {
                                    }
                                }
                            }
                        
                            // RELACIONA PARTIDO ///////////////////////////////////////////////////
                            if(isset($item['autor1']['codPartido']) && is_numeric($item['autor1']['codPartido']) && $item['autor1']['codPartido'] > 0){
                                $res = $t->getDefaultAdapter()->fetchRow('SELECT partido_id FROM pu_partido_de_para WHERE partido_cd_origem=' . $t->getDefaultAdapter()->quoteInto('?', $item['autor1']['codPartido']));
                                if($res){
                                    if($res['partido_id'] > 0){
                                        try {
                                            $t->getDefaultAdapter()->insert('pro_projeto_x_partido', array(
                                                'projeto_id' => $projeto_id,
                                                'partido_id' => $res['partido_id'],
                                                'partido_criacao_id' => 1,
                                              //'partido_participacao_id' => '',
                                              //'partido_pontuacao' => ''
                                            ));
                                        } catch (Exception $exc) {
                                        }
                                    }
                                }else{
                                    try {
                                        $t->getDefaultAdapter()->insert('pu_partido_de_para', array(
                                          //'partido_id' => '',
                                          //'partido_origem_id' => '',
                                            'partido_cd_origem' => $item['autor1']['codPartido'],
                                            'partido_nome' => ((is_string($item['autor1']['txtSiglaPartido']))?($item['autor1']['txtSiglaPartido']):(''))
                                        ));
                                    } catch (Exception $exc) {
                                    }
                                }
                            }
                        }
                    }
                } // loop proposições
            }
            
            Alex_Util::debugEscreve('NOME................: ' . $sigla . '/' . $ano);
            Alex_Util::debugEscreve('QTD TOTAL...........: ' . $qtdTotal);
            Alex_Util::debugEscreve('QTD CADASTROS.......: ' . $qtdCadastro);
            Alex_Util::debugEscreve('QTD TEMPO...........: ' . (time()-$tempo));
        }
    }

    public function atualizarAction() {
        $this->inicilizaLoop();
        $db = Zend_Registry::get('db');
        $tempo = time();
        
//        $sys = new Zend_Session_Namespace('sys');
//        $this->view->contador = $sys->contador = 15080;
//        exit;
        
        // WHERE projeto_ano=2013
        $res = $db->fetchRow('SELECT * FROM pro_projeto ORDER BY projeto_id LIMIT ' . ($this->view->contador-1) . ', 1');
        if($res){
            $itemDetalhe = Camara_Proposicoes::obterProposicaoPorID($res['projeto_cd_origem']);
            $item = Camara_Proposicoes::listarProposicoes(array('sigla' => $itemDetalhe['@attributes']['tipo'], 'numero' => $itemDetalhe['@attributes']['numero'], 'ano' => $itemDetalhe['@attributes']['ano']));
            $item = $item['proposicao'][0];
            
            $projeto = array(
                //'projeto_id'                => null,
                //'projeto_status_id'         => 0,
                //'projeto_origem_id'         => 0,
                //'projeto_cd_origem'         => trim($item['id']),
                //'projeto_url'               => mb_strtolower($sigla . '-' . $item['numero'] . '-' . $item['ano']),
                //'projeto_tplei_id'          => $this->sicTipoProposicao($item['tipoProposicao']),
                //'projeto_numero'            => trim($item['numero']),
                //'projeto_ano'               => trim($item['ano']),
                //'projeto_nome'              => mb_strtoupper(trim($item['nome'])),
                  'projeto_situacao_id'       => $this->sicSituacao($item['situacao']),
                  'projeto_regime_id'         => $this->sicRegime($item['regime']),
                  'projeto_apreciacao_id'     => $this->sicApreciacao($item['apreciacao']),
                  'projeto_ementa'            => trim(((is_string($item['txtEmenta']))?($item['txtEmenta']):(''))),
                  'projeto_ementa_explicada'  => trim(((is_string($item['txtExplicacaoEmenta']))?($item['txtExplicacaoEmenta']):(''))),
                  'projeto_autor'             => $this->trataAutor($item['autor1']),
                  'projeto_link_inteiro_teor' => trim(((is_string($itemDetalhe['LinkInteiroTeor']))?($itemDetalhe['LinkInteiroTeor']):(''))),
                  'projeto_data_apresentacao' => $this->sicDapaApresentacao($item['datApresentacao'])
            );
            
            if(strlen($projeto['projeto_autor']) > 100){
                $projeto['projeto_ementa_explicada'] .= '

AUTOR: ' . $projeto['projeto_autor'];
                $projeto['projeto_autor'] = '';
            }
            
            Alex_Util::debugEscreve('NOME................: ' . $res['projeto_nome']);
            
            if( $projeto['projeto_situacao_id'] != $res['projeto_situacao_id'] ||
                $projeto['projeto_regime_id'] != $res['projeto_regime_id'] ||
                $projeto['projeto_apreciacao_id'] != $res['projeto_apreciacao_id'] ||
                $projeto['projeto_ementa'] != $res['projeto_ementa'] ||
                $projeto['projeto_ementa_explicada'] != $res['projeto_ementa_explicada'] ||
                $projeto['projeto_autor'] != $res['projeto_autor'] ||
                $projeto['projeto_link_inteiro_teor'] != $res['projeto_link_inteiro_teor'] ||
                $projeto['projeto_data_apresentacao'] != $res['projeto_data_apresentacao']
            ){
                if($db->update('pro_projeto', $projeto, 'projeto_id='. $res['projeto_id'])){
                    $qtdAtualizacao++;
                }else{
                    $qtdAtualizacaoFalha++;
                }
            }
            
            // RELACIONA PESSOA ////////////////////////////////////////////////////////////////////
            if(isset($item['autor1']['idecadastro']) && is_numeric($item['autor1']['idecadastro']) && $item['autor1']['idecadastro'] > 0){
                $tmp = $db->fetchRow('SELECT pessoa_id FROM pu_pessoa_de_para WHERE pessoa_cd_origem=' . $db->quoteInto('?', $item['autor1']['idecadastro']));
                if($tmp){
                    if($tmp['pessoa_id'] > 0){
                        try {
                            $db->insert('pro_projeto_x_pessoa', array(
                                'projeto_id' => $res['projeto_id'],
                                'pessoa_id' => $tmp['pessoa_id'],
                                'pessoa_criacao_id' => 1,
                              //'pessoa_participacao_id' => '',
                              //'pessoa_pontuacao' => ''
                            ));
                        } catch (Exception $exc) {
                        }
                    }
                }else{
                    try {
                        $db->insert('pu_pessoa_de_para', array(
                          //'pessoa_id' => '',
                          //'pessoa_origem_id' => '',
                            'pessoa_cd_origem' => $item['autor1']['idecadastro'],
                            'pessoa_nome' =>  ((is_string($item['autor1']['txtNomeAutor']))?($item['autor1']['txtNomeAutor']):('')) . ' - ' . ((is_string($item['autor1']['txtSiglaPartido']))?(trim($item['autor1']['txtSiglaPartido'])):('')) . ' - ' . ((is_string($item['autor1']['txtSiglaUF']))?($item['autor1']['txtSiglaUF']):(''))
                        ));
                    } catch (Exception $exc) {
                    }
                }
            }
            
            // RELACIONA PARTIDO //
            if(isset($item['autor1']['codPartido']) && is_numeric($item['autor1']['codPartido']) && $item['autor1']['codPartido'] > 0){
                $tmp = $db->fetchRow('SELECT partido_id FROM pu_partido_de_para WHERE partido_cd_origem=' . $db->quoteInto('?', $item['autor1']['codPartido']));
                if($tmp){
                    if($tmp['partido_id'] > 0){
                        try {
                            $db->insert('pro_projeto_x_partido', array(
                                'projeto_id' => $res['projeto_id'],
                                'partido_id' => $tmp['partido_id'],
                                'partido_criacao_id' => 1,
                              //'partido_participacao_id' => '',
                              //'partido_pontuacao' => ''
                            ));
                        } catch (Exception $exc) {
                        }
                    }
                }else{
                    try {
                        $db->insert('pu_partido_de_para', array(
                          //'partido_id' => '',
                          //'partido_origem_id' => '',
                            'partido_cd_origem' => $item['autor1']['codPartido'],
                            'partido_nome' => ((is_string($item['autor1']['txtSiglaPartido']))?($item['autor1']['txtSiglaPartido']):(''))
                        ));
                    } catch (Exception $exc) {
                    }
                }
            }
        }else{
            Alex_Util::debugEscreve('ATUALIZAÇÃO COMPLETA');
            $this->finalizaLoop();
        }
        Alex_Util::debugEscreve('QTD TEMPO...........: ' . (time()-$tempo));
    }

    public function votacoesAction() {
        Alex_Util::debugEscreve('EM CONTRUÇÃO.');
    }
    
    public function voteNaWebAction() {
        $this->inicilizaLoop();
        $db = Zend_Registry::get('db');
        $tempo = time();
        
//        $sys = new Zend_Session_Namespace('sys');
//        $this->view->contador = $sys->contador = 112;
//        exit;
        
        $res = $db->fetchAll('
            SELECT *
            FROM pro_projeto
            ORDER BY projeto_ano DESC
            LIMIT ' . (($this->view->contador-1)*500) . ', 500
        ');
        if($res){
            Alex_Util::debugEscreve('QTD PROJETOS........: ' . count($res));
            foreach ($res as $value) {
                $db->update('pro_projeto', array(
                    'projeto_qtd_positivos' => rand(0, 1000),
                    'projeto_qtd_negativos' => rand(0, 1000)
                ), 'projeto_id=' . $value['projeto_id']);
                try {
                    $db->insert('pro_projeto_vw', array(
                        'projeto_id' => $value['projeto_id'],
                        'estado_id' => 6,
                        'projeto_vw_qtd_positivos' => rand(0, 1000),
                        'projeto_vw_qtd_negativos' => rand(0, 1000)
                    ));
                } catch (Exception $exc) {
                }
            }
        }
        
        Alex_Util::debugEscreve('TIME................: ' . date('H:i:s'));
        Alex_Util::debugEscreve('QTD TEMPO...........: ' . (time()-$tempo));
        return;
        
        
        // WHERE projeto_ano=2013
        $res = $db->fetchRow('SELECT * FROM pro_projeto WHERE projeto_ano=2013 ORDER BY projeto_ano  DESC LIMIT ' . ($this->view->contador-1) . ', 1');
        if($res){
            $url = 'http://www.votenaweb.com.br/projetos/' . mb_strtolower(substr($res['projeto_nome'], 0, strpos($res['projeto_nome'], ' '))) . '-' . $res['projeto_numero'] . '-' . $res['projeto_ano'];
            Alex_Util::debugEscreve('URL.................: ' . $url);
            
            try {
                $tmp = curl_init();
                curl_setopt($tmp, CURLOPT_HEADER, 0);
                curl_setopt($tmp, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($tmp, CURLOPT_URL, $url);
                curl_setopt($tmp, CURLOPT_FOLLOWLOCATION, 1);
                $html = curl_exec($tmp);
                curl_close($tmp);
                Alex_Util::debugEscreve('CARREGAMENTO URL....: OK');
            } catch (Exception $exc) {
                Alex_Util::debugEscreve('CARREGAMENTO URL....: ERRO');
                return;
            }
            //echo $html;
            
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
          //@$doc->loadHTMLFile('D:\Desktop\VOTENAWEB Projeto de Lei  PLC 6170-2013.htm');
            
            // RESUMO //
            $tags = $doc->getElementsByTagName('title');
            $titulo = $tags->item(0)->nodeValue;
            if($titulo == 'VOTENAWEB PÃ¡gina nÃ£o encontrada'){
                Alex_Util::debugEscreve('NÃO ENCONTRADA');
                return;
            }
            
            $data = array(
                'projeto_descricao' => '',
                'projeto_resumo' => array(),
                'projeto_autores' => array(),
                'projeto_votacao' => array()
            );
            
            // DESCRICAO //
            $tags = $doc->getElementsByTagName('meta');
            foreach ($tags as $tag) {
                if($tag->getAttribute('name') == 'description'){
                    $data['projeto_descricao'] = utf8_decode($tag->getAttribute('content'));
                }
            }
            
            // RESUMO //
            $tags = $doc->getElementsByTagName('section');
            foreach ($tags as $tag) {
                if($tag->getAttribute('class') == 'main'){
                    $tags = $tag->getElementsByTagName('p');
                    foreach ($tags as $tag) {
                        $data['projeto_resumo'][] = $tag->nodeValue;
                    }
                    $data['projeto_resumo'] = '<div class="p">' . implode('</div><div class="p">', $data['projeto_resumo']) . '</div>';
                    break;
                }
            }

            // ATORES //
            $tags = $doc->getElementsByTagName('aside');
            foreach ($tags as $tag) {
                if($tag->getAttribute('class') == 'sidebar'){
                    $tags = $tag->getElementsByTagName('div');
                    foreach ($tags as $tag) {
                        if($tag->getAttribute('class') == 'politician-small'){
                            $data['projeto_autores'][] = trim($tag->nodeValue);
                        }
                        //
                        // PEGAR A CATEGORIA
                        //
                    }
                    break;
                }
            }

            // VOTAÇÃO NACIONAL //
            $tags = $doc->getElementsByTagName('div');
            foreach ($tags as $tag) {
                if($tag->getAttribute('class') == 'container details even'){
                    $tags = $tag->getElementsByTagName('div');
                    foreach ($tags as $tag) {
                        if($tag->getAttribute('class') == 'filters'){
                            $total = $up = $down = 0;
                            $tags = $tag->getElementsByTagName('span');
                            foreach ($tags as $tag) {
                                if($tag->getAttribute('class') == 'total'){
                                    $total = substr($tag->nodeValue, 0, strpos($tag->nodeValue, ' '));
                                }elseif($tag->getAttribute('class') == 'up'){
                                    $up = substr($tag->nodeValue, 0, strpos($tag->nodeValue, ' '));
                                }elseif($tag->getAttribute('class') == 'down'){
                                    $down = substr($tag->nodeValue, 0, strpos($tag->nodeValue, ' '));
                                }
                            }
                            if($total == $up+$down){
                                $data['projeto_votacao'][] = array(
                                    'estado'    => 6,
                                    'total'     => $total,
                                    'up'        => $up,
                                    'down'      => $down
                                );
                            }
                            break;
                        }
                    }
                    break;
                }
            }

            // VOTAÇÃO ESTADUAL //
            $tags = $doc->getElementsByTagName('script');
            foreach ($tags as $tag) {
                if($tag->getAttribute('type') == 'text/javascript'){
                    $script = $tag->nodeValue;
                    if(strpos($script, 'window.votes')){
                        $script = preg_replace('/\s+|\s+/', '', $script);
                        $script = str_replace('"', '', $script);
                        $script = substr($script, strpos($script, 'window.votes'));
                        $script = substr($script, strpos($script, '[')+2, strpos($script, 'window.colors')-18);
                        $script = explode('},{', $script);
                        
                        foreach ($script as $key => $value) {
                            $script[$key] = explode(',', $value);
                        }
                        foreach ($script as $key => $value) {
                            $estado = $this->getEstadoId(substr($value[0], strpos($value[0], ':')+1));
                            if($estado){
                                $data['projeto_votacao'][] = array(
                                    'estado'    => $estado,
                                    'up'        => substr($value[1], strpos($value[1], ':')+1),
                                    'down'      => substr($value[2], strpos($value[2], ':')+1),
                                );
                            }
                        }
                        break;
                    }
                }
            }
            
            // ARMAZENAMENTO //
            if(true){
                $db->update('pro_projeto', array(
                    'projeto_descricao' => $data['projeto_descricao'],
                    'projeto_resumo' => $data['projeto_resumo'],
                ), 'projeto_id=' . $res['projeto_id']);

                foreach ($data['projeto_votacao'] as $value) {
                    try {
                        $db->insert('pro_projeto_vw', array(
                            'projeto_id' => $res['projeto_id'],
                            'estado_id' => $value['estado'],
                            'projeto_vw_qtd_positivos' => $value['up'],
                            'projeto_vw_qtd_negativos' => $value['down']
                        ));
                    } catch (Exception $exc) {
                        $db->update('pro_projeto_vw', array(
                            'projeto_vw_qtd_positivos' => $value['up'],
                            'projeto_vw_qtd_negativos' => $value['down']
                        ), 'projeto_id=' . $res['projeto_id'] . ' && estado_id=' . $value['estado']);
                    }
                }
            }
            //Alex_Util::debugEscreve($data);
        }
        Alex_Util::debugEscreve('QTD TEMPO...........: ' . (time()-$tempo));
    }
    
    public function rankingPoliticosAction() {
        $this->inicilizaLoop();
        $db = Zend_Registry::get('db');
        $tempo = time();
        
        $res = $db->fetchAll('
            SELECT PP.pessoa_id, P.projeto_ano AS rank_ano, sum(PP.pessoa_pontuacao) AS rank_qtd_pontos, sum(P.projeto_qtd_positivos) AS rank_qtd_positivos, sum(P.projeto_qtd_negativos) AS rank_qtd_negativos
            FROM pro_projeto_x_pessoa PP, pro_projeto P
            WHERE PP.projeto_id=P.projeto_id
            GROUP BY PP.pessoa_id, P.projeto_ano
            ORDER BY PP.pessoa_id
            LIMIT ' . (($this->view->contador-1)*500) . ', 500
        ');
        if($res){
            foreach ($res as $value) {
                try {
                    $db->insert('pu_pessoa_x_ranking', $value);
                } catch (Exception $exc) {
                    $db->update('pu_pessoa_x_ranking', $value, 'rank_ano=' . $value['rank_ano'] . ' && pessoa_id=' . $value['pessoa_id']);
                }
            }
        }else{
            Alex_Util::debugEscreve('ATUALIZAÇÃO COMPLETA');
            $this->finalizaLoop();
        }
        Alex_Util::debugEscreve('QTD TEMPO...........: ' . (time()-$tempo));
        
        $sys = new Zend_Session_Namespace('sys');
        $this->view->contador = $sys->contador = 0;
        //exit;
    }
    
    public function rankingPartidosAction() {
        $this->inicilizaLoop();
        $db = Zend_Registry::get('db');
        $tempo = time();
        
        $res = $db->fetchAll('
            SELECT PP.partido_id, P.projeto_ano AS rank_ano, sum(PP.partido_pontuacao) AS rank_qtd_pontos, sum(P.projeto_qtd_positivos) AS rank_qtd_positivos, sum(P.projeto_qtd_negativos) AS rank_qtd_negativos
            FROM pro_projeto_x_partido PP, pro_projeto P
            WHERE PP.projeto_id=P.projeto_id
            GROUP BY PP.partido_id, P.projeto_ano
            ORDER BY PP.partido_id
            LIMIT ' . (($this->view->contador-1)*500) . ', 500
        ');
        if($res){
            foreach ($res as $value) {
                try {
                    $db->insert('pu_partido_x_ranking', $value);
                } catch (Exception $exc) {
                    $db->update('pu_partido_x_ranking', $value, 'rank_ano=' . $value['rank_ano'] . ' && partido_id=' . $value['partido_id']);
                }
            }
        }else{
            Alex_Util::debugEscreve('ATUALIZAÇÃO COMPLETA');
            $this->finalizaLoop();
        }
        Alex_Util::debugEscreve('QTD TEMPO...........: ' . (time()-$tempo));
        
        $sys = new Zend_Session_Namespace('sys');
        $this->view->contador = $sys->contador = 0;
        //exit;
    }
    
    public function indexAction() {
        $this->finalizaLoop();
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////
    
    private function trataAutor($data){
        $string = '';
        if(is_string($data['txtNomeAutor'])){
            $string .= trim($data['txtNomeAutor']);
        }
        if(is_string($data['txtSiglaPartido'])){
            $string .= ' - ' . trim($data['txtSiglaPartido']);
            if(is_string($data['txtSiglaUF'])){
                $string .= '/' . trim($data['txtSiglaUF']);
            }
        }else{
            if(is_string($data['txtSiglaUF'])){
                $string .= ' - ' . trim($data['txtSiglaUF']);
            }
        }
        $string .= '|';
        if(is_string($data['idecadastro'])){
            $string .= trim($data['idecadastro']);
        }
        $string .= '|';
        if(is_string($data['codPartido'])){
            $string .= trim($data['codPartido']);
        }
        return$string;
    }

    private function sicTipoProposicao($data) {
        if(is_string($data['id']) && is_numeric($data['id']) && $data['id'] > 0 && is_string($data['sigla']) && is_string($data['nome'])){
            $db = Zend_Registry::get('db');
            $res = $db->fetchAll('SELECT * FROM pro_tipo_lei WHERE tplei_cd_origem=' . $db->quoteInto('?', $data['id']));
            if(count($res) > 1){
                // ERRO //
                Alex_Util::debugEscreve('ERRO: Há mais de uma TIPO com código ' . $data['id']);
            }elseif(count($res) == 1){
                // ATUALIZAR //
                $db->update('pro_tipo_lei', array(
                  //'tplei_id'           => '',
                  //'tplei_grupo_id'    => '',
                  //'tplei_origem_id'    => '',
                  //'tplei_cd_origem'    => $data['id'],
                    'tplei_sigla'        => trim($data['sigla']),
                    'tplei_nome'         => trim($data['nome'])
                ), 'tplei_id=' . $res[0]['tplei_id']);
                return $res[0]['tplei_id'];
            }else{
                // CADASTRAR //
                $res = new Zend_Db_Table(array('name' => 'pro_tipo_lei'));
                $id = $res->insert(array(
                  //'tplei_id'           => '',
                  //'tplei_grupo_id'    => '',
                  //'tplei_origem_id'    => '',
                    'tplei_cd_origem'    => $data['id'],
                    'tplei_sigla'        => trim($data['sigla']),
                    'tplei_nome'         => trim($data['nome']),
                    'tplei_descricao'    => trim($data['nome'])
                ));
                return $id;
            }
        }
        return 1;
    }
    
    private function sicSituacao($data) {
        if(is_string($data['id']) && is_numeric($data['id']) && $data['id'] > 0 && is_string($data['descricao'])){
            $db = Zend_Registry::get('db');
            $res = $db->fetchAll('SELECT * FROM pro_situacao WHERE situacao_cd_origem=' . $db->quoteInto('?', $data['id']));
            if(count($res) > 1){
                // ERRO //
                Alex_Util::debugEscreve('ERRO: Há mais de uma SITUAÇÃO com código ' . $data['id']);
            }elseif(count($res) == 1){
                // ATUALIZAR //
                $db->update('pro_situacao', array(
                  //'situacao_id'           => '',
                  //'situacao_grupo_id'    => '',
                  //'situacao_origem_id'    => '',
                  //'situacao_cd_origem'    => $data['id'],
                    'situacao_nome'         => trim($data['descricao'])
                ), 'situacao_id=' . $res[0]['situacao_id']);
                return $res[0]['situacao_id'];
            }else{
                // CADASTRAR //
                $res = new Zend_Db_Table(array('name' => 'pro_situacao'));
                $id = $res->insert(array(
                  //'situacao_id'           => '',
                  //'situacao_grupo_id'    => '',
                  //'situacao_origem_id'    => '',
                    'situacao_cd_origem'    => $data['id'],
                    'situacao_nome'         => trim($data['descricao']),
                    'situacao_descricao'    => trim($data['descricao'])
                ));
                return $id;
            }
        }
        return 1;
    }
    
    private function sicApreciacao($data) {
        if(is_string($data['id']) && is_numeric($data['id']) && $data['id'] > 0 && is_string($data['txtApreciacao'])){
            $db = Zend_Registry::get('db');
            $res = $db->fetchAll('SELECT * FROM pro_apreciacao WHERE apreciacao_cd_origem=' . $db->quoteInto('?', $data['id']));
            if(count($res) > 1){
                // ERRO //
                Alex_Util::debugEscreve('ERRO: Há mais de uma APRECIAÇÃO com código ' . $data['id']);
            }elseif(count($res) == 1){
                // ATUALIZAR //
                $db->update('pro_apreciacao', array(
                  //'apreciacao_id'           => '',
                  //'apreciacao_grupo_id'    => '',
                  //'apreciacao_origem_id'    => '',
                  //'apreciacao_cd_origem'    => $data['id'],
                    'apreciacao_nome'         => trim($data['txtApreciacao'])
                ), 'apreciacao_id=' . $res[0]['apreciacao_id']);
                return $res[0]['apreciacao_id'];
            }else{
                // CADASTRAR //
                $res = new Zend_Db_Table(array('name' => 'pro_apreciacao'));
                $id = $res->insert(array(
                  //'apreciacao_id'           => '',
                  //'apreciacao_grupo_id'    => '',
                  //'apreciacao_origem_id'    => '',
                    'apreciacao_cd_origem'    => $data['id'],
                    'apreciacao_nome'         => trim($data['txtApreciacao']),
                    'apreciacao_descricao'    => trim($data['txtApreciacao'])
                ));
                return $id;
            }
        }
        return 1;
    }
    
    private function sicRegime($data) {
        if(is_string($data['codRegime']) && is_numeric($data['codRegime']) && $data['codRegime'] > 0 && is_string($data['txtRegime'])){
            $db = Zend_Registry::get('db');
            $res = $db->fetchAll('SELECT * FROM pro_regime WHERE regime_cd_origem=' . $db->quoteInto('?', $data['codRegime']));
            if(count($res) > 1){
                // ERRO //
                Alex_Util::debugEscreve('ERRO: Há mais de uma REGIME com código ' . $data['codRegime']);
            }elseif(count($res) == 1){
                // ATUALIZAR //
                $db->update('pro_regime', array(
                  //'regime_id'           => '',
                  //'regime_grupo_id'    => '',
                  //'regime_origem_id'    => '',
                  //'regime_cd_origem'    => $data['codRegime'],
                    'regime_nome'         => trim($data['txtRegime'])
                ), 'regime_id=' . $res[0]['regime_id']);
                return $res[0]['regime_id'];
            }else{
                // CADASTRAR //
                $res = new Zend_Db_Table(array('name' => 'pro_regime'));
                $id = $res->insert(array(
                  //'regime_id'           => '',
                  //'regime_grupo_id'    => '',
                  //'regime_origem_id'    => '',
                    'regime_cd_origem'    => $data['codRegime'],
                    'regime_nome'         => trim($data['txtRegime']),
                    'regime_descricao'    => trim($data['txtRegime'])
                ));
                return $id;
            }
        }
        return 1;
    }

    private function sicDapaApresentacao($data){
        $data = explode(' ', trim($data));
        $data = explode('/', $data[0]);
        if(count($data) == 3){
            if(checkdate($data[1], $data[0], $data[2])){
                return strtotime($data[0] . '-' . $data[1] . '-' . $data[2]);
            }
        }
        return 0;
    }
    
    private function getEstadoId($nome) {
        $lista = array(
            'acre'                  => '1',
            'alagoas'               => '2',
            'amazonas'              => '3',
            'amapa'                 => '4',
            'bahia'                 => '5',
            'ceara'                 => '7',
            'distrito-federal'      => '8',
            'espirito-santo'        => '9',
            'goias'                 => '10',
            'maranhao'              => '11',
            'minas-gerais'          => '12',
            'mato-grosso-do-sul'    => '13',
            'mato-grosso'           => '14',
            'para'                  => '15',
            'paraiba'               => '16',
            'pernambuco'            => '17',
            'piaui'                 => '18',
            'parana'                => '19',
            'rio-de-janeiro'        => '20',
            'rio-grande-do-norte'   => '21',
            'rondonia'              => '22',
            'roraima'               => '23',
            'rio-grande-do-sul'     => '24',
            'santa-catarina'        => '25',
            'sergipe'               => '26',
            'sao-paulo'             => '27',
            'tocantins'             => '28',
        );
        
        if(isset($lista[$nome])){
            return $lista[$nome];
        }else{
            return false;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function init() {
        echo '
            <a href="/escravo/proposicoes">Home</a>
            <a href="/escravo/proposicoes/siglas">Siglas</a>
            <a href="/escravo/proposicoes/cadastrar">Cadastrar</a>
            <a href="/escravo/proposicoes/atualizar">Atualizar</a>
            <a href="/escravo/proposicoes/votacoes">Votações</a>
            <a href="/escravo/proposicoes/ranking-politicos">Ranking-Políticos</a>
            <a href="/escravo/proposicoes/ranking-partidos">Ranking-Partidos</a>
            <a href="/escravo/proposicoes/vote-na-web">VoteNaWeb</a>
            <hr/>
        ';
        
        if(Zend_Auth::getInstance()->hasIdentity()
        && Zend_Auth::getInstance()->getIdentity()->user_id == 1
        && Zend_Auth::getInstance()->getIdentity()->user_tipo_id == 3){
        }else{
            $this->_redirect('/');
        }
        $this->view->contador = 0;
    }
    
    public function inicilizaLoop(){
        if(true){
            $sys = new Zend_Session_Namespace('sys');
            if(isset($sys->contador)){
                $sys->contador+=1;
            }else{
                $sys->contador=0;
            }
            $this->view->contador = $sys->contador;
            Alex_Util::debugEscreve('CONTADOR............: ' . $sys->contador);
        }
        if($sys->contador > 97600){
            $this->finalizaLoop();
        }
    }
    
    public function finalizaLoop(){
        $sys = new Zend_Session_Namespace('sys');
        $this->view->contador = $sys->contador = 0;
        Alex_Util::debugEscreve('LOOP FINALIZADO.....: OK');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //$client = new Zend_Http_Client('http://www.opovounido.com/Deputados.xml', array('timeout' => 60));
    //$data = $client->request(Zend_Http_Client::POST)->getBody();
    //$data = (array) simplexml_load_string($data);
    //$data = Camara_WebService::simpleXmlToArray($data);
    //$data = $data['Deputados']['Deputado'];
    //$cd=0;
    //$db = Zend_Registry::get('db');
    //foreach ($data as $value) {
    //    $res = $db->fetchRow('SELECT * FROM pu_pessoa_de_para WHERE pessoa_cd_origem=' . $value['ideCadastro']);
    //    if($res){
    //    }else{
    //        $db->insert('pu_pessoa_de_para', array(
    //            'pessoa_cd_origem' => $value['ideCadastro'],
    //            'pessoa_nome' => $value['nomeParlamentar'] . ' ' . $value['LegendaPartidoEleito'] . '/' . $value['UFEleito'] . ' | ' . $value['Profissao']
    //        ));
    //        $cd++;
    //    }
    //}
    //Alex_Util::debugEscreve('CADASTRADOS.........: ' . $cd);
    
}