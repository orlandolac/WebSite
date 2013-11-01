<?php

class Extracao_ArquivosController extends Zend_Controller_Action {
    
    protected $ANO = 2012;
    protected $ENTRADA;
    protected $SAIDA;
    protected $SEGUNDO_TURNO;

    public function indexAction() {
        // Os registro do arquivo BR de 1994 conterão um campo extra que deverá ser removido manualmente.
        $in = time();
        echo '<b>Processando dados...</b>';
        
//        $this->SEGUNDO_TURNO = fopen($this->SAIDA . '/segundo_turno.txt', 'w+');
//        for ($ano = 2012; $ano >= 1994; $ano-=2) {
//            $this->ANO = $ano;
//            $ini = time();
//            
//            // DADOS //
////            $this->processarCargos();
////            $this->processarPartidos();
////            $this->processarEscolaridades();
////            $this->processarEstadosCivis();
////            $this->processarEstados();
////            $this->processarMunicipios();
////            $this->processarNascionalidades();
////            $this->processarOcupacoes();
////            $this->processarSexos();
////            $this->processarSituacoesCandidatura();
////            $this->processarSituacoesTotalizacao();
////            $this->processarTiposBem();
//            
//            // REGISTROS //
////            $this->processarVagas();
//            $this->processarCandidatos();
////            $this->processarBens();
////            $this->processarNomes();
////            $this->processarLegendas();
//
//            echo "\n\n<b>" . $this->ANO . " - Processos concluídos em................ ";
//            printf('% 9s segundo(s).</b>', number_format((time() - $ini), 0, ",", "."));
//            echo '<hr>';
//        }
//        fclose($this->SEGUNDO_TURNO);
        
        printf("\n" . '<b>Processor finalizados em.....................: % 9s segundo(s)</b>.', number_format((time() - $in), 0, ",", "."));
    }

    // DADOS //
    
    public function processarCargos() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>cargos</u>..................... ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (!isset($lista[$cps[8]])) {
                        $lista[$cps[8]] = self::trataTexto($cps[9]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/VAGA');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/VAGA/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO < 2012) {
                        $cps = explode('";"', substr($linha, 1, -3));
                    } else {
                        $cps = explode('";"', substr($linha, 1, -2));
                    }
                    if (count($cps) != 10) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 10 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (!isset($lista[$cps[7]])) {
                        $lista[$cps[7]] = self::trataTexto($cps[8]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_cargos.txt', 'w+');
        fwrite($f, '"' . join('";"', array('cargo_id', 'cargo_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarPartidos() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>partidos</u>................... ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (!isset($lista[$cps[16]])) {
                        $cps[18] = self::trataTexto($cps[18]);
                        $lista[$cps[16]] = array(
                            'sigla' => $cps[17],
                            'nome' => $cps[18]
                        );
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_partidos.txt', 'w+');
        fwrite($f, '"' . join('";"', array('part_numero', 'part_sigla', 'part_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value['sigla'] . '";"' . $value['nome'] . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarEscolaridades() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>escolaridades</u>.............. ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (!isset($lista[$cps[30]])) {
                        $lista[$cps[30]] = self::trataTexto($cps[31]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_escolaridades.txt', 'w+');
        fwrite($f, '"' . join('";"', array('escolari_id', 'escolari_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarEstadosCivis() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u></u>estados civis</u>.............. ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (!isset($lista[$cps[32]])) {
                        $lista[$cps[32]] = self::trataTexto($cps[33]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_estados_civis.txt', 'w+');
        fwrite($f, '"' . join('";"', array('estciv_id', 'estciv_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarEstados() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>estados</u>.................... ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (!isset($lista[$cps[5]])) {
                        $lista[$cps[5]] = self::trataTexto($cps[7]);
                        $c++;
                        if ($cps[5] == $cps[6]) {
                            $lista[$cps[5]] = $cps[7];
                        } else {
                            $lista[$cps[5]] = 0;
                        }
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_estados.txt', 'w+');
        fwrite($f, '"' . join('";"', array('uf_id', 'uf_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarMunicipios() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>municipios</u>................. ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (strlen($cps[6]) == 2 && !is_numeric($cps[6])) {
                        if ($cps[6] == 'ZZ' || $cps[6] == 'VT') {
                            Alex_Util::debugEscreve('O arquivo ' . $arq . ' tem registros com codigo de municipio ZZ ou VT. Você deve análizar-los.');
                            exit(0);
                        }
                        continue;
                    } else {
                        $cps[6] = (int) $cps[6];
                        if (!isset($lista[$cps[6]])) {
                            $c++;
                            $lista[$cps[6]] = array(
                                'uf_id' => strtoupper($cps[5]),
                                'mun_nome' => self::trataTexto($cps[7])
                            );
                        }
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_municipios.txt', 'w+');
        fwrite($f, '"' . join('";"', array('uf_id', 'mun_id', 'mun_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $value['uf_id'] . '";"' . $key . '";"' . $value['mun_nome'] . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarNascionalidades() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>nascionalidades</u>............ ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (!isset($lista[$cps[34]])) {
                        $lista[$cps[34]] = self::trataTexto($cps[35]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_nacionalidades.txt', 'w+');
        fwrite($f, '"' . join('";"', array('nasc_id', 'nasc_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarOcupacoes() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>ocupações</u>.................. ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (!isset($lista[$cps[23]])) {
                        $lista[$cps[23]] = self::trataTexto($cps[24]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_ocupacoes.txt', 'w+');
        fwrite($f, '"' . join('";"', array('ocup_id', 'ocup_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarSexos() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>sexos</u>...................... ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    if (!isset($lista[$cps[28]])) {
                        $lista[$cps[28]] = self::trataTexto($cps[29]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_sexos.txt', 'w+');
        fwrite($f, '"' . join('";"', array('sexo_id', 'sexo_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarSituacoesCandidatura() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>situações de candidatura</u>... ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    $cps[14] = self::negativoParaZero($cps[14]);
                    if (!isset($lista[$cps[14]])) {
                        $lista[$cps[14]] = self::trataTexto($cps[15]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_situacoes_candidatura.txt', 'w+');
        fwrite($f, '"' . join('";"', array('sitcand_id', 'sitcand_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarSituacoesTotalizacao() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>situações de totalizacao</u>... ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 42 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    $cps[40] = self::negativoParaZero($cps[40]);
                    if (!isset($lista[$cps[40]])) {
                        $lista[$cps[40]] = self::trataTexto($cps[41]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_situacoes_totalizacao.txt', 'w+');
        fwrite($f, '"' . join('";"', array('sittotal_id', 'sittotal_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarTiposBem() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>tipos de bem</u>............... ';
        $lista = array();
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/BEM');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/BEM/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 12) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 12 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    $cps[6] = self::negativoParaZero($cps[6]);
                    if (!isset($lista[$cps[6]])) {
                        $lista[$cps[6]] = self::trataTexto($cps[7]);
                        $c++;
                    }
                }
                fclose($f);
            }
        }
        $f = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_bens_tipo.txt', 'w+');
        fwrite($f, '"' . join('";"', array('bemtp_id', 'bemtp_nome')) . '"' . "\n");
        foreach ($lista as $key => $value) {
            fwrite($f, '"' . $key . '";"' . $value . '"' . "\n");
        }
        fclose($f);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    // REGISTROS //
    
    public function processarVagas() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>vagas</u>...................... ';
        $lista = array();
        $fSaida = fopen($this->SAIDA . '/' . $this->ANO . '/' . $this->ANO . '_vagas.txt', 'w+');
        fwrite($fSaida, '"' . join('";"', array('elei_ano', 'uf_id', 'mun_id', 'cargo_id', 'qtd_vagas')) . '"' . "\n");
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/VAGA');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/VAGA/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO < 2012) {
                        $cps = explode('";"', substr($linha, 1, -3));
                    } else {
                        $cps = explode('";"', substr($linha, 1, -2));
                    }
                    if (count($cps) != 10) {
                        Alex_Util::debugEscreve($arq . ' possue um registro fora do layout de 10 campos. O registro tem ' . count($cps) . ' campos.');
                        exit(0);
                    }
                    $cps[] = $this->getTimes($cps[0], $cps[1]);
                    unset($cps[0]);
                    unset($cps[1]);
                    unset($cps[3]);
                    unset($cps[3]);
                    unset($cps[6]);
                    unset($cps[8]);
                    if (strlen($cps[5]) == 2 && !is_numeric($cps[5])) {
                        $cps[5] = 0;
                    } else {
                        $cps[5] = (int) $cps[5];
                    }
                    $c++;
                    fwrite($fSaida, '"' . join('";"', $cps) . '"' . "\n");
                }
                fclose($f);
            }
        }
        fclose($fSaida);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarCandidatos() {
        echo "\n\n" . $this->ANO . ' - Processando <u>candidatos</u>................. ';
        $c = 0;
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                $fSaida = fopen($this->SAIDA . '/' . $this->ANO . '/CANDIDATO/' . $this->ANO . '_canditatos_' . substr($arq, -6, -4) . '.txt', 'w+');
                fwrite($fSaida, '"' . join('";"', array('elei_ano', 'uf_id', 'mun_id', 'cargo_id', 'cand_nome', 'cand_codigo_db_tse', 'candid_urna_numero', 'candid_urna_nome', 'sitcand_id', 'part_numero', 'legenda_codigo_db_tse', 'ocup_id', 'cand_nascimento_data', 'cand_titulo_eleitor', 'sexo_id', 'escolari_id', 'estciv_id', 'nasc_id', 'cand_nascimento_uf_id', 'cand_nascimento_mun_id', 'cand_nascimento_mun_nome', 'candid_despesa_maxima', 'sittotal_id')) . '"' . "\n");
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve('O arquivo ' . $arq . ' tem registros fora do layout. Qtd Campos: ' . count($cps));
                        exit(0);
                    }
                    $cps[] = $this->getTimes($cps[0], $cps[1]);
                    unset($cps[0]);
                    unset($cps[1]);
                    unset($cps[4]);
                    unset($cps[7]);
                    unset($cps[9]);
                    unset($cps[15]);
                    unset($cps[17]);
                    unset($cps[18]);
                    unset($cps[20]);
                    unset($cps[21]);
                    unset($cps[22]);
                    unset($cps[24]);
                    unset($cps[27]);
                    unset($cps[29]);
                    unset($cps[31]);
                    unset($cps[33]);
                    unset($cps[35]);
                    unset($cps[41]);
                    $cps[10] = self::trataTexto($cps[10]);
                    $cps[13] = self::trataTexto($cps[13]);
                    $cps[25] = $this->converterData($cps[25]);
                    $cps[37] = self::negativoParaZero($cps[37]);
                    $cps[38] = self::trataTexto($cps[38]);
                    $cps[40] = self::negativoParaZero($cps[40]);
                    if($cps[3] == 1){
                        unset($cps[3]);
                        fwrite($fSaida, '"' . join('";"', $cps) . '"' . "\n");
                    }else{
                        fwrite($this->SEGUNDO_TURNO, '"' . join('";"', $cps) . '"' . "\n");
                    }
                    $c++;
                }
                fclose($fSaida);
                fclose($f);
            }
        }
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }
    
    public function processarNomes() {
        if(true){
            $f = fopen($this->SAIDA . '/nomes.txt', 'r');
            $db = Zend_Registry::get('db');
            while ($linha = fgets($f)) {
                try {
                    $db->insert('pu_pessoa_chave', array('chave_nome' => $linha));
                } catch (Exception $exc) {
                }
            }
            fclose($f);
        }
        
        return;
        
        echo "\n\n" . $this->ANO . ' - Processando <u>nomes</u>................. ';
        $c = 0;
        $fSaida = fopen($this->SAIDA . '/nomes.txt', 'a');
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO');
        $palavras = array();
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
                while ($linha = fgets($f)) {
                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
                    }
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 42) {
                        Alex_Util::debugEscreve('O arquivo ' . $arq . ' tem registros fora do layout. Qtd Campos: ' . count($cps));
                        exit(0);
                    }
                    $c++;
                    $nomes = $cps[10] . ' ' . $cps[13];
                    $nomes = trim(mb_strtolower(utf8_encode($nomes)));
                    $nomes = preg_replace("/'/", '`', $nomes);
                    $nomes = preg_replace("/\s+/", ' ', $nomes);
                    $nomes = explode(' ', $nomes);
                    $nomes = array_unique($nomes);
                    sort($nomes);
                    
                    foreach ($nomes as $value) {
                        $palavras[] = $value;
                        if(memory_get_usage() > 32000000){
                            if(memory_get_usage() > 128000000){
                                Alex_Util::debugEscreve('LIMITE DE MEMORIA: ' . memory_get_usage());
                                exit;
                            }
                            $palavras = array_unique($palavras);
                            sort($palavras);
                            fwrite($fSaida, implode("\n", $palavras));
                            unset($palavras);
                            $palavras = array();
                            Alex_Util::debugEscreve(memory_get_usage());
                        }
                    }
                }
                fclose($f);
            }
        }
        fclose($fSaida);
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarBens() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>bens</u>....................... ';
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/BEM');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/BEM/' . $arq, 'r');
                $fSaida = fopen($this->SAIDA . '/' . $this->ANO . '/BEM/' . $this->ANO . '_bens_' . substr($arq, -6, -4) . '.txt', 'w+');
                fwrite($fSaida, '"' . join('";"', array('elei_ano', 'uf_id', 'cand_codigo_db_tse', 'bemtp_id', 'bemtp_nome', 'bem_nome', 'bem_valor')) . '"' . "\n");
                while ($linha = fgets($f)) {
                    $cps = explode('";"', substr($linha, 1, -2));
                    if (count($cps) != 12) {
                        Alex_Util::debugEscreve('O arquivo ' . $arq . ' tem registros fora do layout. Qtd Campos: ' . count($cps));
                        exit(0);
                    }
                    $cps[] = self::getTimes($cps[0], $cps[1]);
                    unset($cps[0]);
                    unset($cps[1]);
                    unset($cps[3]);
                    unset($cps[10]);
                    unset($cps[11]);
                    $cps[6] = self::negativoParaZero($cps[6]);
                    $cps[8] = preg_replace("/'/", '`', $cps[8]);
                    fwrite($fSaida, '"' . join('";"', $cps) . '"' . "\n");
                    $c++;
                }
                fclose($fSaida);
                fclose($f);
            }
        }
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    public function processarLegendas() {
        $c = 0;
        echo "\n\n" . $this->ANO . ' - Processando <u>legendas</u>................... ';
        $arqs = opendir($this->ENTRADA . '/' . $this->ANO . '/LEGENDA');
        while ($arq = readdir($arqs)) {
            if (substr($arq, -4) == '.txt') {
//                $f = fopen($this->ENTRADA . '/' . $this->ANO . '/CANDIDATO/' . $arq, 'r');
//                $fSaida = fopen($this->SAIDA . '/' . $this->ANO . '/LEGENDA/' . $this->ANO . '_legendas_' . substr($arq, -6, -4) . '.txt', 'w+');
//                fwrite($fSaida, '"' . join('";"', array('elei_ano', 'uf_id', 'mun_id', 'legenda_sigla', 'legenda_composicao', 'legenda_nome')) . '"' . "\n");
//                while ($linha = fgets($f)) {
//                    if ($this->ANO == 1998 && substr($arq, -6) != 'BR.txt') {
//                        $linha = str_replace('"#NE#";"#NE#"', '"#NE#"', $linha);
//                    }
//                    $cps = explode('";"', substr($linha, 1, -2));
//                    if (count($cps) != 42) {
//                        Alex_Util::debugEscreve('O arquivo ' . $arq . ' tem registros fora do layout. Qtd Campos: ' . count($cps));
//                        exit(0);
//                    }
//                    $cps[] = self::getTimes($cps[0], $cps[1]);
//                    unset($cps[0]);
//                    unset($cps[1]);
//                    unset($cps[3]);
//                    unset($cps[4]);
//                    unset($cps[7]);
//                    unset($cps[8]);
//                    unset($cps[9]);
//                    unset($cps[10]);
//                    unset($cps[11]);
//                    unset($cps[12]);
//                    unset($cps[13]);
//                    unset($cps[14]);
//                    unset($cps[15]);
//                    unset($cps[16]);
//                    unset($cps[17]);
//                    unset($cps[18]);
//                    unset($cps[19]);
//                    unset($cps[23]);
//                    unset($cps[24]);
//                    unset($cps[25]);
//                    unset($cps[26]);
//                    unset($cps[27]);
//                    unset($cps[28]);
//                    unset($cps[29]);
//                    unset($cps[30]);
//                    unset($cps[31]);
//                    unset($cps[32]);
//                    unset($cps[33]);
//                    unset($cps[34]);
//                    unset($cps[35]);
//                    unset($cps[36]);
//                    unset($cps[37]);
//                    unset($cps[38]);
//                    unset($cps[39]);
//                    unset($cps[40]);
//                    unset($cps[41]);
//                    if (strlen($cps[6]) == 2 && !is_numeric($cps[6])) {
//                        if ($cps[6] == 'ZZ' || $cps[6] == 'VT') {
//                            Alex_Util::debugEscreve('O arquivo ' . $arq . ' tem registros com codigo de municipio ZZ ou VT. Você deve analizar-los.');
//                            exit(0);
//                        }
//                        $cps[6] = 0;
//                    } else {
//                        $cps[6] = (int) $cps[6];
//                    }
//                    $cps[20] = self::trataTexto($cps[20]);
//                    $cps[22] = self::trataTexto($cps[22]);
//                    fwrite($fSaida, '"' . join('";"', $cps) . '"' . "\n");
//                    $c++;
//                }
//                fclose($fSaida);
//                fclose($f);
            }
        }
        printf('% 9s encontrado(s).', number_format($c, 0, ",", "."));
    }

    // CONVERSORES //

    public static function trataTexto($texto) {
        $texto = utf8_encode($texto);
        $texto = mb_strtolower($texto);
        $texto = ucwords($texto);
        $texto = preg_replace("/'/", '`', $texto);
        return $texto;
    }

    public static function negativoParaZero($num) {
        if ($num < 0) {
            return 0;
        }
        return $num;
    }
        
    public static function converterMes($mes) {
        switch ($mes) {
            case 'JAN' : return '01';
            case 'FEB' : return '02';
            case 'MAR' : return '03';
            case 'APR' : return '04';
            case 'MAY' : return '05';
            case 'JUN' : return '06';
            case 'JUL' : return '07';
            case 'AUG' : return '08';
            case 'SEP' : return '09';
            case 'OCT' : return '10';
            case 'NOV' : return '11';
            case 'DEC' : return '12';
        }
    }
    
    public static function getTimes($data, $hora) {
        $dataParts = explode('/', $data);
        $horaParts = explode(':', $hora);
        return mktime($horaParts[0], $horaParts[1], $horaParts[2], $dataParts[1], $dataParts[0], $dataParts[2]);
    }
    
    public function converterData($data) {
        // 94 -> 16/03/49
        // 96 -> 05/06/55
        // 98 -> 08031955
        // 00 -> 28121967
        // 02 -> 31051964
        // 04 -> 18011972
        // 06 -> 14/07/1957
        // 08 -> 06-NOV-78
        // 10 -> 12-OCT-71
        // 12 -> 05-JUL-68
        
        if(strlen($data) < 8 || strlen($data) > 10){
            return '0000-00-00';
        }else{
            if($this->ANO <= 1996){
                $part = explode('/', $data);
                if(count($part) != 3){
                    return '0000-00-00';
                }else{
                    return '19' . $part[2] . '-' . $part[1] . '-' . $part[0];
                }
            }elseif($this->ANO <= 2004){
                if(strlen($data) != 8){
                    return '0000-00-00';
                }else{
                    return substr($data, -4) . '-' . substr($data, 2, 2) . '-' . substr($data, 0, 2);
                }
            }elseif($this->ANO == 2006){
                if(strlen($data) != 10){
                    return '0000-00-00';
                }else{
                    $part = explode('/', $data);
                    if(count($part) != 3){
                        return '0000-00-00';
                    }else{
                        return $part[2] . '-' . $part[1] . '-' . $part[0];
                    }
                }
            }else{
                if(strlen($data) != 9){
                    return '0000-00-00';
                }else{
                    $part = explode('-', $data);
                    if(count($part) != 3){
                        return '0000-00-00';
                    }else{
                        return '19' . $part[2] . '-' . self::converterMes($part[1]) . '-' . $part[0];
                    }
                }
            }
        }
    }

    // MÉTODOS //
    
    public function init() {
        $this->ENTRADA = APPLICATION_PATH . '/../../o-povo-unido-data/ENTRADA/';
        $this->SAIDA = APPLICATION_PATH . '/../../o-povo-unido-data/SAIDA/';
    }
    
    public function preDispatch() {
        echo '<pre>';
    }

    public function posDispatch() {
        echo '</pre>';
    }

}