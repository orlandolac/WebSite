<?php

class Extracao_ScriptsController extends Zend_Controller_Action {

    protected $ANO = 2012;
    protected $ENTRADA;
    protected $SAIDA;
    protected $municipios = array();
    
    protected $SQL;
    protected $ARQ;
    
    public function indexAction() {
        $contador = new Zend_Session_Namespace('cont');
        if(isset($contador->ano)){
            $contador->ano -= 2;
            if($contador->ano < 1994){
                $contador->ano = 2012;
            }
        }else{
            $contador->ano = 2012;
        }
        $this->ANO = $contador->ano;
        $this->view->ano = $this->ANO;

        $ini = time();
        echo "\n\nELEIÇÕES DE <u>" . $this->ANO . "</u>";

        //$this->carregarVagas();
        //$this->carregarPoliticosCandidaturas();
        //$this->carregarBens();

        echo "\n\n<b>" . $this->ANO . " - Processos concluídos em................ ";
        printf('% 9s segundo(s).</b>', number_format((time() - $ini), 0, ",", "."));
    }

    // REGISTROS //

    public function carregarPoliticosCandidaturas() {
        $tPess = new Zend_Db_Table(array('name' => 'pu_pessoa'));
        $tCand = new Zend_Db_Table(array('name' => 'pu_candidatura'));
        $tTerm = new Zend_Db_Table(array('name' => 'pu_pessoa_termo'));
        
        $qtd = $com_titulo = $sem_titulo = $dup_titulo = $com_codigo = $sem_codigo = 0;
        $arqs = opendir($this->SAIDA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            //if (substr($arq, -6) == 'AC.txt') {
            //if (substr($arq, 0, 17) == '2012_canditatos_M') {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->SAIDA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                fgets($f);
                while ($linha = fgets($f)) {
                    $cps = explode('";"', substr($linha, 1, -2));
                    
                    $pess = array(
                      //'pessoa_id'                 => null,
                      //'pessoa_status'             => 1,
                        'pessoa_titulo_eleitor'     => $this->limpaTitulo($cps[13]),
                      //'pessoa_cpf'                => '',
                      //'pessoa_url'                => '',
                        'pessoa_nome'               => $this->trataNome($cps[4]),
                        'pessoa_apelido'            => $this->trataNome($cps[7]), // nome na urna
                      //'pessoa_diretorio'          => ''
                    );
                    $pessDet = array(
                        'pessoa_id'                 => 0,
                      //'pessoa_rg'                 => '',
                        'pessoa_sexo_id'            => $this->converteSexo($cps[14]),
                        'pessoa_nacionalidade_id'   => $this->converteNacionalidade($cps[17]),
                        'pessoa_nat_estado_id'      => $this->converteEstado($cps[18]),
                        'pessoa_nat_municipio_id'   => $cps[19],
                        'pessoa_data_nascimento'    => $cps[12],
                        'pessoa_ocupacao_id'        => $this->converteOcupacao($cps[11]),
                        'pessoa_escolaridade_id'    => $this->converteEscolaridade($cps[15]),
                        'pessoa_estado_civil_id'    => $this->converteEstadoCivil($cps[16]),
                      //'pessoa_end_estado_id'      => '',
                      //'pessoa_end_municipio_id'   => '',
                        'pessoa_endereco'           => $cps[20], // $cps[20] é o nome do municipio em q nasceu... é apenas temporário.
                      //'pessoa_telefone'           => '',
                      //'pessoa_fax'                => '',
                      //'pessoa_email'              => '',
                      //'pessoa_site'               => '',
                        'pessoa_data_ini'           => time(),
                        'pessoa_data_alt'           => $cps[23],
                      //'pessoa_registros'          => $linha,
                        'refer_id'                  => 1
                    );
                    $cand = array(
                      //'cand_id'                   => 0,
                        'pessoa_id'                 => 0,
                        'eleicao_id'                => $cps[0],
                        'cand_estado_id'            => $this->converteEstado($cps[1]),
                        'cand_municipio_id'         => $this->converteMunicipio($cps[2]),
                        'cargo_id'                  => $this->converteCargo($cps[3]),
                        'partido_id'                => $this->convertePartido($cps[9]),
                      //'legenda_id'                => 0,
                      //'turno_id'                  => 0,
                      //'cand_status'               => 1,
                      //'cand_aux_id'               => 0,
                        'cand_urna_numero'          => $cps[6],
                        'cand_urna_nome'            => $pess['pessoa_apelido'],
                        'cand_status_cand'          => $this->converteSitCandidatura($cps[8]),
                        'cand_status_total'         => $this->converteSitTotalizacao($cps[22])
                    );
                    $candDet = array(
                        'cand_id'                   => 0,
                        'cand_ocupacao_id'          => $pessDet['pessoa_ocupacao_id'],
                        'cand_escolaridade_id'      => $pessDet['pessoa_escolaridade_id'],
                        'cand_estado_civil_id'      => $pessDet['pessoa_estado_civil_id'],
                        'cand_idade'                => $this->getIdade($cps[0], $cps[12]),
                      //'cand_total_patrimonio'     => 0,
                        'cand_total_verba'          => $cps[21],
                      //'cand_total_votos'          => 0,
                        'cand_data_ini'             => time(),
                        'cand_data_alt'             => $cps[23],
                        'cand_pessoa_cd_tse'        => $cps[5],
                        'refer_id'                  => 1
                    );
                    
                    $ttl = false;
                    if($pess['pessoa_titulo_eleitor'] > 0){
                        $ttl = true;
                        $com_titulo++;
                    }else{
                        $sem_titulo++;
                    }
                    
                    // RELACIONA COM TITULO CADASTRADO //
                    //if($ttl){
                    //    try {
                    //        $res = $tPess->fetchRow('pessoa_titulo_eleitor=' . $pess['pessoa_titulo_eleitor']);
                    //    } catch (Exception $exc) {
                    //        Alex_Util::debugEscreve($pess);
                    //        echo $exc->getTraceAsString();
                    //        exit;
                    //    }
                    //    if($res){
                    //        $pessDet['pessoa_id'] = $res['pessoa_id'];
                    //        $tPess->getDefaultAdapter()->update('pu_pessoa_detalhe', array(
                    //            'pessoa_registros' => new Zend_Db_Expr("CONCAT(pessoa_registros, '|" . $linha . "')")
                    //        ), 'pessoa_id=' . $res['pessoa_id']);
                    //    }
                    //}
                    
                    if(!($pessDet['pessoa_id'] > 0)){
                        $pessDet['pessoa_id'] = $tPess->insert($pess);
                        $tPess->getDefaultAdapter()->insert('pu_pessoa_detalhe', $pessDet);
                    }
                    if($pessDet['pessoa_id'] > 0){
                        $cand['pessoa_id'] = $pessDet['pessoa_id'];
                        $candDet['cand_id'] = $tCand->insert($cand);
                        if($candDet['cand_id'] > 0){
                            $tCand->getDefaultAdapter()->insert('pu_candidatura_detalhe', $candDet);
                            
                            if($cps[5] > 0){ // Se tiver Código-TSE registra no De-Para
                                $com_codigo++;
                            }else{
                                $sem_codigo++;
                            }
                            
                            // RELACIONA COM TERMO //
                            $termos = $this->trataTermo($pess['pessoa_nome'] . ' ' . $pess['pessoa_apelido']);
                            $termos = explode(' ', $termos);
                            $termos = array_unique($termos);
                            sort($termos);
                            
                            foreach ($termos as $key => $termo) {
                                if(is_numeric($termo)){
                                    unset($termos[$key]);
                                }else{
                                    switch ($termo) {
                                        case 'a':
                                        case 'á':
                                        case 'as':
                                        case 'e':
                                        case 'o':
                                        case 'i':
                                        case 'u':
                                        case 'as':
                                        case 'ou':
                                        case 'na':
                                        case 'nas':
                                        case 'no':
                                        case 'nos':
                                        case 'da':
                                        case 'das':
                                        case 'de':
                                        case 'des':
                                        case 'do':
                                        case 'dos':
                                        case 'em':
                                        case 'com':
                                        case 'pra':
                                        case '':
                                        case ' ': unset($termos[$key]);
                                    }
                                }
                            }
                            
                            foreach ($termos as $termo) {
                                $termo_id = 0;
                                $t = $tTerm->fetchRow($tTerm->getDefaultAdapter()->quoteInto('termo_nome=?', $termo));
                                if($t){
                                    $termo_id = $t['termo_id'];
                                }else{
                                    $termo_id = $tTerm->insert(array(
                                        'termo_nome' => $termo
                                    ));
                                }
                                if($termo_id > 0){
                                    try {
                                        $tTerm->getDefaultAdapter()->insert('pu_pessoa_termo_indice', array(
                                            'termo_id' => $termo_id,
                                            'pessoa_id' => $cand['pessoa_id']
                                        ));
                                    } catch (Exception $exc) {
                                    }
                                }else{
                                    Alex_Util::debugEscreve('NÃO GEROU ID DO TERMO');
                                    Alex_Util::debugEscreve($cps);
                                    exit;
                                }
                            }
                        }else{
                            Alex_Util::debugEscreve('NÃO GEROU ID DA CANDIDATURA');
                            Alex_Util::debugEscreve($cps);
                            exit;
                        }
                    }else{
                        Alex_Util::debugEscreve('NÃO GEROU ID DA PESSOA');
                        Alex_Util::debugEscreve($cps);
                        exit;
                    }
                    $qtd++;
                }
                fclose($f);
            }
        }
        
        Alex_Util::debugEscreve('Qtd. de Registros..............: ' . $qtd);
        Alex_Util::debugEscreve('Qtd. de Com Título.............: ' . $com_titulo);
        Alex_Util::debugEscreve('Qtd. de Sem Título.............: ' . $sem_titulo);
        Alex_Util::debugEscreve('Qtd. de Título Duplicados......: ' . $dup_titulo);
        Alex_Util::debugEscreve('Qtd. de Com Código.............: ' . $com_codigo);
        Alex_Util::debugEscreve('Qtd. de Sem Código.............: ' . $sem_codigo);
    }

    public function carregarBens(){
        $qtd = 0;
        $db = Zend_Registry::get('db');
        $arqs = opendir($this->SAIDA . '/' . $this->ANO . '/BEM');
        $qtdNenhum = $qtdMultiplos = 0;
        $data = array();
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->SAIDA . '/' . $this->ANO . '/BEM/' . $arq, 'r');
                fgets($f);
                while ($linha = fgets($f)) {
                    $cps = explode('";"', substr($linha, 1, -2));
                    $qtd++;
                    
                    //0 "elei_ano";
                    //1 "uf_id";
                    //2 "cand_codigo_db_tse";
                    //3 "bemtp_id";
                    //4 "bemtp_nome";
                    //5 "bem_nome";
                    //6 "bem_valor";
                    //7 "times"
                    
                    //0 "2012";
                    //1 "AC";
                    //2 "10000000563";
                    //3 "12";
                    //4 "CASA";
                    //5 "01 (uma) Casa De Alvenaria, Localizada Ã  Rua Palheral, 636 Â¿ Cidade Nova Â¿ Rio Branco/ac, Medindo 12x35";
                    //6 "180000";
                    //7 "1362350028"
                    
                    $data = array(
                      //'bem_id'                => null,
                        'pessoa_id'             => 0,           // recuperar do bd
                        'eleicao_id'            => $cps[0],
                        'estado_id'             => $cps[1],     // converter
                      //'bem_status'            => 0,
                        'bem_tipo_id'           => $cps[3],
                        'bem_valor'             => $cps[6],
                        'bem_nome'              => $cps[5],
                        'bem_data_ini'          => time(),
                        'bem_data_alt'          => $cps[7],
                        'bem_pessoa_cd_tse'     => $cps[2],
                      //'refer_id'              => 0
                    );
                    
                    if(!Default_Model_Lista_Eleicao::get($data['eleicao_id'])){
                        Alex_Util::debugEscreve('ELEIÇÃO INVÁLIDA');
                        Alex_Util::debugEscreve($data);
                        exit;
                    }
                    $uf = Default_Model_Lista_EstadoSigla::get($data['estado_id']);
                    if(is_array($uf)){
                        if($uf[0] > 0 && $uf[0] < 29){
                            $data['estado_id'] = $uf[0];

                            $res = $db->fetchAll('
                                SELECT C.pessoa_id
                                FROM pu_candidatura C, pu_candidatura_detalhe D
                                WHERE C.eleicao_id=' . $db->quoteInto('?', $data['eleicao_id']) . '
                                   && C.cand_estado_id=' . $data['estado_id'] . '
                                   && D.cand_pessoa_cd_tse=' . $data['bem_pessoa_cd_tse'] . '
                                   && C.cand_id=d.cand_id
                            ');
                            
                            if(count($res) > 0){
                                if(count($res) == 1){
                                    $data['pessoa_id'] = $res[0]['pessoa_id'];
                                }else{
                                    $qtdMultiplos++;
                                }
                            }else{
                                $qtdNenhum++;
                            }
                            
                            $db->insert('pu_bem', $data);
                        }else{
                            Alex_Util::debugEscreve('UF EXTRA');
                            Alex_Util::debugEscreve($data);
                            exit;
                        }
                    }else{
                        Alex_Util::debugEscreve('UF INVÁLIDO');
                        Alex_Util::debugEscreve($data);
                        exit;
                    }
                    
                }
                fclose($f);
            }
        }
        
        Alex_Util::debugEscreve($data);
        Alex_Util::debugEscreve('QTD NENHUM.....................: ' . $qtdNenhum);
        Alex_Util::debugEscreve('QTD MULTIPLOS..................: ' . $qtdMultiplos);
        Alex_Util::debugEscreve('QTD REGISTROS..................: ' . $qtd);
    }
    
    public function carregarVagas() {
        $db = Zend_Registry::get('db');

        $erro = 0;
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_vagas.txt', 'r');
        fgets($f);
        while ($linha = fgets($f)) {
            $cps = explode('";"', substr($linha, 1, -2));
            
            $data = array(
                'eleicao_id'            => $cps[0],
                'estado_id'             => $this->converteEstado($cps[1]),
                'municipio_id'          => $this->converteMunicipio($cps[2]),
                'cargo_id'              => $this->converteCargo($cps[3]),
                'cargo_qtd_vagas'       => $cps[4],
                'vaga_data_ini'         => time(),
                'vaga_data_alt'         => $cps[5],
                'fonte_informacao_id'   => 1
            );
            
            try {
                $db->insert('pu_eleicao_x_cargo', $data);
            } catch (Exception $exc) {
                Alex_Util::debugEscreve('Problema na inclusão.');
                Alex_Util::debugEscreve($data);
                Alex_Util::debugEscreve($exc);
                $erro++;
            }
        }
        fclose($f);
        Alex_Util::debugEscreve('Erro(s): ' . $erro);
    }

    // CONVERSORES //

    public function converteLegenda($id) {
        return $id;
    }
    
    public function convertePartido($id) {
        
        
        if($this->ANO <= 2006 && $this->ANO >= 1994){ // 2006, 2004, 2002, 2000, 1998, 1996, 1994
            switch ($id) {
                case 22: return 9998; //   Partido Liberal
                case 25: return 9999; //   Partido da Frente Liberal
            }
        }
        if($this->ANO <= 2002 && $this->ANO >= 1996){ // 2002, 2000, 1998, 1996
            switch ($id) {
                case 11: return 9997; //   Partido Progressista Brasileiro
                case 90: return 0;
            }
        }
        if($this->ANO <= 2000){ // 2000, 1998, 1996, 1994
            if($id == 36){
                return 9995; //   Partido da Reconstrução Nacional
            }
        }
        if($this->ANO == 1998 || $this->ANO == 1996){
            if($id == 31){
                return 9994; //   Partido da Solidariedade Nacional
            }
        }
        if($this->ANO == 1994){
            switch ($id) {
                case 11: return 9993; //   Partido Progressista Reformador
                case 17: return 9996; //   Partido Trabalhista Renovador Brasileiro
                case 39: return 9992; //   Partido Progressista
            }
        }
        return $id;
    }

    public function converteOcupacao($id) {
        if ($this->ANO == 1994) {
            return 0;
        } elseif ($this->ANO == 1998 || $this->ANO == 2000) {
            switch ($id) {
                case 214: return 232;  // delegado de policia
                case 216: return 295;  // oficiais das forcas armadas e forcas auxiliares
                case 113: return 9991; // Enfermeiro e Nutricionista
                case 291: return 9992; // Ocupante de Cargo de Direção e Assessoramento Intermediário
                case 211: return 9993; // Procurador e Assemelhados
                case 521: return 9994; // Governanta de Hotel, Camareiro, Porteiro, Cozinheiro e Garçom
                case 391: return 9995; // Chefe Intermediário
                case 158: return 9996; // Desenhista Técnico
                case 215: return 9997; // Ocupante de Cargo de Direção e Assessoramento Superior
                case 128: return 9998; // Astrônomo e Meteorologista
                case 112: return 9999; // Veterinário e Zootecnista
            }
        }
        return $id;
    }

    public function converteCargo($id) {
        if ($this->ANO == 1994) {
            switch ($id) {
                case 7: return 6;
                case 8: return 7;
            }
        } elseif ($this->ANO == 1996) {
            switch ($id) {
                case 9: return 11;
                case 11: return 13;
            }
        }
        return $id;
    }

    public function converteMunicipio($id) {
        $id = $id + 0;
        if ($id > 0) {
            if(!count($this->municipios) > 0){
                $res = Zend_Registry::get('db')->fetchAll('SELECT * FROM pu_municipio');
                foreach ($res as $value) {
                    $this->municipios[$value['municipio_cd_tse']] = $value['municipio_id'];
                }
            }
            
            if(isset($this->municipios[$id])){
                return $this->municipios[$id];
            }
        }elseif($id == 0){
            return 0;
        }
        
        Alex_Util::debugEscreve('Municipio Inválido: ' . $id);
        exit;
    }
    
    // CONSTANTES //
    
    public function converteEstado($id) {
        if (strlen($id) == 2) {
            $uf =  Default_Model_Lista_EstadoSigla::get($id);
            if($uf){
                return $uf[0];
            }
        }
        return 0;
    }
    
    public function converteSexo($id) {
        if ($id == 2) {
            return 'M'; // Masculino
        } elseif ($id == 4) {
            return 'F'; // Feminino
        }
        return '-';
    }
    
    public function converteEscolaridade($id) {
        if ( $this->ANO <= 1996 ){ // 1994 1996
            return 0;
        }
        return $id;
    }
    
    public function converteNacionalidade($id) {
        if ( $this->ANO <= 1996 ){ // 1994 1996
            return 0;
        }
        return $id;
    }

    public function converteEstadoCivil($id) {
        if ( $this->ANO <= 1996 ){ // 1994 1996
            return 0;
        }
        return $id;
    }

    public function converteSitTotalizacao($id) {
        if ($this->ANO == 1994) {
            switch ($id) {
                case 5: return 6;
                case 6: return 5;
            }
        }
        if ($this->ANO == 2012) {
            switch ($id) {
                case 2: return 99;
                case 3: return 5;
                case 5: return 2;
            }
        }
        if ($id > 0) {
            return $id;
        }
        return 0;
    }

    public function converteSitCandidatura($id) {
        if ($this->ANO == 1994 || $this->ANO == 1996) {
            return 0;
        } elseif ($this->ANO == 2004 || $this->ANO == 2002) {
            if ($id == 4) {
                return 8;
            }
        } elseif ($this->ANO == 2000) {
            switch ($id) {
                case 4: return 8;
                case 8: return 11;
                case 9: return 13;
            }
        } elseif ($this->ANO == 1998) {
            switch ($id) {
                case 4: return 8;
                case 8: return 5;
                case 9: return 13;
            }
        }
        return $id;
    }

    public function getIdade($eleicao, $data){
        $dataParts = explode('-', $data);
        if($dataParts[1] > 10){
            return $eleicao - $dataParts[0] -1;
        }else{
            return $eleicao - $dataParts[0];
        }
    }
        
    public function limpaTitulo($titulo) {
        $titulo = trim($titulo);
        $titulo = preg_replace('/-/', '', $titulo);
        $titulo = preg_replace('/ /', '', $titulo);
        return $titulo;
    }

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
        $nome = str_replace(' Nas ', ' nas ', $nome);
        $nome = str_replace(' No ', ' no ', $nome);
        $nome = str_replace(' Nos ', ' nos ', $nome);
        $nome = str_replace(' Da ', ' da ', $nome);
        $nome = str_replace(' Das ', ' das ', $nome);
        $nome = str_replace(' De ', ' de ', $nome);
        $nome = str_replace(' Des ', ' des ', $nome);
        $nome = str_replace(' Do ', ' do ', $nome);
        $nome = str_replace(' Dos ', ' dos ', $nome);
        $nome = str_replace(' Para ', ' para ', $nome);
        $nome = str_replace(' Pra ', ' pra ', $nome);
        $nome = str_replace(' Em ', ' em ', $nome);
        $nome = str_replace(' Com ', ' com ', $nome);
        $nome = trim($nome);
        return $nome;
    }
    
    public function trataUrl($nome) {
        $nome = mb_strtolower($nome);
        $nome = str_replace('ç', 'c', $nome);
        $nome = preg_replace('/(á)|(à)|(â)|(ä)|(ã)|(ª)/', 'a', $nome);
        $nome = preg_replace('/(é)|(è)|(ê)|(ë)/', 'e', $nome);
        $nome = preg_replace('/(í)|(ì)|(î)|(ï)/', 'i', $nome);
        $nome = preg_replace('/(ó)|(ò)|(ô)|(ö)|(õ)|(º)|(°)/', 'o', $nome);
        $nome = preg_replace('/(ú)|(ù)|(û)|(ü)/', 'u', $nome);
        $nome = preg_replace('/ /', '-', $nome);
        $nome = preg_replace('/[^a-z0-9-]/', '', $nome);
        return $nome;
    }
    
    public function trataTermo($nome) {
        $nome = mb_strtolower($nome);
        $nome = str_replace('.', '', $nome);
        $nome = str_replace(',', '', $nome);
        $nome = str_replace(':', '', $nome);
        $nome = str_replace(';', '', $nome);
        $nome = str_replace('?', '', $nome);
        $nome = str_replace('!', '', $nome);
        $nome = str_replace('@', '', $nome);
        $nome = str_replace('#', '', $nome);
        $nome = str_replace('$', '', $nome);
        $nome = str_replace('%', '', $nome);
        $nome = str_replace('&', '', $nome);
        $nome = str_replace('*', '', $nome);
        $nome = str_replace('(', '', $nome);
        $nome = str_replace(')', '', $nome);
        $nome = str_replace('_', '', $nome);
        $nome = str_replace('-', '', $nome);
        $nome = str_replace('=', '', $nome);
        $nome = str_replace('+', '', $nome);
        $nome = str_replace('/', '', $nome);
        $nome = str_replace('"', '', $nome);
        $nome = str_replace("'", '', $nome);
        $nome = str_replace('`', '', $nome);
        $nome = str_replace('´', '', $nome);
        $nome = str_replace('^', '', $nome);
        $nome = str_replace('~', '', $nome);
        $nome = str_replace('{', '', $nome);
        $nome = str_replace('}', '', $nome);
        $nome = str_replace('[', '', $nome);
        $nome = str_replace(']', '', $nome);
        $nome = str_replace('ª', '', $nome);
        $nome = str_replace('º', '', $nome);
        $nome = str_replace('°', '', $nome);
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