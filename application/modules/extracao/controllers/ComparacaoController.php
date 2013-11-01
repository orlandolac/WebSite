<?php

class Extracao_ComparacaoController extends Zend_Controller_Action {

    protected $ANO = 1994;
    protected $ENTRADA;
    protected $SAIDA;
    protected $SQL;
    protected $ARQ;
    protected $lista = array();
    protected $politico_id = 0;
    
    public function indexAction() {
            $ini = time();
            echo "\n\nELEIÇÕES DE <u>" . $this->ANO . "</u>";
            
            //$this->compararBensTipo();
            //$this->compararOcupacoes();
            //$this->compararCargos();
            //$this->compararPartidos();
            //$this->compararMunicipios();

            echo "\n\n\n<b>Processos concluídos em: " . (time() - $ini) . " segundo(s)</b>.<hr>";
    }

    public function compararBensTipo(){
        $new = $old = array();
        
        // Recupera os registrados
        $res = Zend_Registry::get('db')->fetchAll('SELECT bem_tipo_id, bem_tipo_nome FROM pu_bem_tipo');
        foreach ($res as $value) {
            $old[$value['bem_tipo_id']] = $value;
        }
        
        // Compara com os novos
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_bens_tipo.txt', 'r');
        Alex_Util::debugEscreve(fgets($f));
        while ($linha = fgets($f)) {
            $cps = explode('";"', substr($linha, 1, -2));
            if(count($old) > 0 && isset($old[$cps[0]])){
                $new = $old[$cps[0]];
                $new['------------>'] = $cps[1];
                Alex_Util::debugEscreve($new);
            }else{
                Alex_Util::debugEscreve('CADASTRE: ' . implode(', ', $cps));
            }
        }
        fclose($f);
    }
    
    public function compararOcupacoes(){
        $new = $old = array();
        
        // Recupera os registrados
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('SELECT ocupacao_id, ocupacao_nome FROM pu_ocupacao');
        foreach ($res as $value) {
            $old[$value['ocupacao_id']] = $value;
        }
        
        // Compara com os novos
        $cadastre = 0;
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_ocupacoes.txt', 'r');
        Alex_Util::debugEscreve(fgets($f));
        while ($linha = fgets($f)) {
            $cps = explode('";"', substr($linha, 1, -2));
            if(count($old) > 0 && isset($old[$cps[0]])){
                $new = $old[$cps[0]];
                if($this->preparaComparacao($new['ocupacao_nome']) == $this->preparaComparacao($cps[1])){
                    
                }else{
                    $new['------------>'] = $cps[1];
                    Alex_Util::debugEscreve($new);
                }
            }else{
                $cadastre++;
                $data = array(
                    'ocupacao_id' => $cps[0],
                    'ocupacao_nome' => $this->trataNome($cps[1])
                );
                Alex_Util::debugEscreve('CADASTRE: ' . implode(', ', $data));
                if(false){
                    $db->insert('pu_ocupacao', $data);
                }
            }
        }
        fclose($f);
        Alex_Util::debugEscreve('A SER CADASTRADO: '. $cadastre);
    }
    
    public function compararCargos(){
        $new = $old = array();
        
        // Recupera os registrados
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('SELECT cargo_id, cargo_nome FROM pu_cargo');
        foreach ($res as $value) {
            $old[$value['cargo_id']] = $value;
        }
        
        // Compara com os novos
        $cadastre = 0;
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_cargos.txt', 'r');
        Alex_Util::debugEscreve(fgets($f));
        while ($linha = fgets($f)) {
            $cps = explode('";"', substr($linha, 1, -2));
            if(count($old) > 0 && isset($old[$cps[0]])){
                $new = $old[$cps[0]];
                if($this->preparaComparacao($new['cargo_nome']) == $this->preparaComparacao($cps[1])){
                }else{
                    $new['--------->'] = $cps[1];
                    Alex_Util::debugEscreve($new);
                }
            }else{
                $cadastre++;
                $data = array(
                    'cargo_id' => $cps[0],
                    'cargo_nome' => $this->trataNome($cps[1])
                );
                Alex_Util::debugEscreve('CADASTRE: ' . implode(', ', $data));
                if(false){
                    $db->insert('pu_cargo', $data);
                }
            }
        }
        fclose($f);
        Alex_Util::debugEscreve('A SER CADASTRADO: '. $cadastre);
    }
    
    public function compararPartidos(){
        $new = $old = array();
        
        // Recupera os novos registros
        $cadastre = 0;
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_partidos.txt', 'r');
        Alex_Util::debugEscreve(fgets($f));
        while ($linha = fgets($f)) {
            $cps = explode('";"', substr($linha, 1, -2));
            $cps[2] = $this->trataNome($cps[2]);
            $new[$cps[0]] = $cps;
        }
        fclose($f);
        
        // Compara com os registrados
        $db = Zend_Registry::get('db');
        foreach ($new as $key => $value) {
            $res = $db->fetchAll('
                SELECT partido_id, partido_numero, partido_sigla, partido_nome
                FROM pu_partido
                WHERE partido_numero=' . $key . '
                   || partido_sigla =\'' . $value[1] . '\'
                   || partido_nome  =\'' . $value[2] . '\'
            ');
            if(count($res) > 0){
                $value[] = $res;
                
                if(count($res) == 1
                    && $value[0] == $value[3][0]['partido_id']
                    && $value[0] == $value[3][0]['partido_numero']
                    && $value[1] == $value[3][0]['partido_sigla']
                    && $value[2] == $value[3][0]['partido_nome']
                ){
                    continue;
                }
                Alex_Util::debugEscreve($value);
            }else{
                Alex_Util::debugEscreve('NADA SEMELHANTE À:');
                Alex_Util::debugEscreve($value);
                if(false){
                    $db->insert('pu_partido', array(
                        'partido_id' => $value[0],
                        'partido_numero' => $value[0],
                        'partido_sigla'  => $value[1],
                        'partido_nome'   => $value[2],
                    ));
                }
            }
        }
        
        
        
        
        
//        Alex_Util::debugEscreve('A SER CADASTRADO: '. $cadastre);
//            if(count($old) > 0 && isset($old[$cps[0]])){
//                $new = $old[$cps[0]];
//                if($this->preparaComparacao($new['cargo_nome']) == $this->preparaComparacao($cps[1])){
//                }else{
//                    $new['--------->'] = $cps[1];
//                    Alex_Util::debugEscreve($new);
//                }
//            }else{
//                $cadastre++;
//                $data = array(
//                    'cargo_id' => $cps[0],
//                    'cargo_nome' => $this->trataNome($cps[1])
//                );
//                Alex_Util::debugEscreve('CADASTRE: ' . implode(', ', $data));
//                if(false){
//                    $db->insert('pu_cargo', $data);
//                }
//            }
    }
    
    public function compararMunicipios() {
        $d = $w = $z = 0;
        $db = Zend_Registry::get('db');
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_municipios.txt', 'r');
        Alex_Util::debugEscreve(fgets($f));
        while ($linha = fgets($f)) {
            $cps = explode('";"', substr($linha, 1, -2));
            $res = $db->fetchRow('SELECT * FROM pu_municipio WHERE municipio_cd_tse=' . $cps[1]);
            if ($res) {
                $res['municipio_nome'] = strtolower($res['municipio_nome']);
                $cps[2] = strtolower($cps[2]);
                if ($res['municipio_nome'] != $cps[2]) {
                    $cps[2] = $this->limpaNome($cps[2]);
                    $res['municipio_nome'] = $this->limpaNome($res['municipio_nome']);
                    if ($res['municipio_nome'] == $cps[2]) {
                        $z++;
                        continue;
                    }
                    Alex_Util::debugEscreve(array(
                        'uf_id' => $cps[0],
                        'municipio_id' => $cps[1],
                        'municipio_nome' => $cps[2],
                        'municipio_nom_' => $res['municipio_nome']
                    ));
                    $d++;
                } else {
                    $w++;
                }
            } else {
                $data = array(
                    'uf_id' => $cps[0],
                    'municipio_id' => $cps[1],
                    'municipio_nome' => $cps[2],
                    'municipio_nom_' => '  \_____________Não foi relacionada.'
                );
                Alex_Util::debugEscreve($data);
            }
        }
        Alex_Util::debugEscreve($d . ' são diferentes. ');
        Alex_Util::debugEscreve($w . ' são iguais. ');
        Alex_Util::debugEscreve($z . ' são semelhantes. ');
        fclose($f);
    }

    // FUNÇÕES //

    public function trataNome($nome){
        $nome = ucwords($nome);
        $nome = str_replace(' A ', ' a ', $nome);
        $nome = str_replace(' Á ', ' á ', $nome);
        $nome = str_replace(' As ', ' as ', $nome);
        $nome = str_replace(' E ', ' e ', $nome);
        $nome = str_replace(' É ', ' é ', $nome);
        $nome = str_replace(' O ', ' o ', $nome);
        $nome = str_replace(' Ou ', ' ou ', $nome);
        $nome = str_replace(' Na ', ' na ', $nome);
        $nome = str_replace(' No ', ' no ', $nome);
        $nome = str_replace(' Da ', ' da ', $nome);
        $nome = str_replace(' De ', ' de ', $nome);
        $nome = str_replace(' Do ', ' do ', $nome);
        $nome = str_replace(' Das ', ' das ', $nome);
        $nome = str_replace(' Dos ', ' dos ', $nome);
        $nome = str_replace(' Para ', ' para ', $nome);
        $nome = str_replace(' Pra ', ' pra ', $nome);
        $nome = str_replace(' Em ', ' em ', $nome);
        $nome = str_replace(' Com ', ' com ', $nome);
        return $nome;
    }
    
    public function preparaComparacao($nome) {
        $nome = mb_strtolower($nome);
        $nome = $this->limpaNome($nome);
        return $nome;
    }

    public function limpaNome($nome){
        $nome = str_replace('ç', 'c', $nome);
        $nome = preg_replace('/(á)|(à)|(â)|(ä)|(ã)/', 'a', $nome);
        $nome = preg_replace('/(é)|(è)|(ê)|(ë)/', 'e', $nome);
        $nome = preg_replace('/(í)|(ì)|(î)|(ï)/', 'i', $nome);
        $nome = preg_replace('/(ó)|(ò)|(ô)|(ö)|(õ)/', 'o', $nome);
        $nome = preg_replace('/(ú)|(ù)|(û)|(ü)/', 'u', $nome);
        $nome = preg_replace('/-/', ' ', $nome);
        $nome = preg_replace('/`/', ' ', $nome);
        $nome = preg_replace('/"/', ' ', $nome);
        return $nome;
    }

    // MÉTODOS //
    
    public function init() {
        $this->ENTRADA = APPLICATION_PATH . '/../../o-povo-unido-data/ENTRADA/';
        $this->SAIDA = APPLICATION_PATH . '/../../o-povo-unido-data/SAIDA/';
        $this->SQL = APPLICATION_PATH . '/../../o-povo-unido-data/SQL/';
    }

    public function preDispatch() {
        echo '<pre>';
    }

    public function posDispatch() {
        echo '</pre>';
    }

}