<?php

class Extracao_IndexController extends Zend_Controller_Action {

    public $pagina = 100;
    public $destino = 'D:/Studio/Sites/Alex/o-povo-unido-data/FOTOS';
    
    public function init() {
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
    
    // AÇÃO //

    public static function simplificaNome($string){
        $string = mb_strtolower($string);
        $string = str_replace('ç', 'c', $string);
        $string = str_replace('w', 'v', $string);
        $string = str_replace('y', 'i', $string);
        $string = str_replace('z', 's', $string);
        $string = preg_replace('/(á)|(à)|(â)|(ä)|(ã)|(ª)|(@)/', 'a', $string);
        $string = preg_replace('/(é)|(è)|(ê)|(ë)|(&)/', 'e', $string);
        $string = preg_replace('/(í)|(ì)|(î)|(ï)/', 'i', $string);
        $string = preg_replace('/(ó)|(ò)|(ô)|(ö)|(õ)|(º)/', 'o', $string);
        $string = preg_replace('/(ú)|(ù)|(û)|(ü)/', 'u', $string);
        $string = preg_replace('/[^a-z0-9\s$]/', ' ', $string);
        $string = ' ' . $string . ' ';

        $string = str_replace('ll', 'l', $string);
        $string = str_replace(' dr ', ' ', $string);
        $string = str_replace(' dro ', ' ', $string);
        $string = str_replace(' dra ', ' ', $string);
        $string = str_replace(' doutor ', ' ', $string);
        $string = str_replace(' doutora ', ' ', $string);
        $string = str_replace(' prof ', ' ', $string);
        $string = str_replace(' profo ', ' ', $string);
        $string = str_replace(' profa ', ' ', $string);
        $string = str_replace(' professor ', ' ', $string);
        $string = str_replace(' professora ', ' ', $string);
        $string = str_replace(' irma ', ' ', $string);
        $string = str_replace(' irmao ', ' ', $string);
        $string = str_replace(' pastor ', ' ', $string);
        $string = str_replace(' bispo ', ' ', $string);
        $string = str_replace(' motorista ', ' ', $string);
        $string = str_replace(' relojoeiro ', ' ', $string);
        $string = str_replace(' vendedor ', ' ', $string);
        $string = str_replace(' inss ', ' ', $string);
        $string = str_replace(' pt ', ' ', $string);
        $string = str_replace(' cabo ', ' ', $string);
        $string = str_replace(' soldado ', ' ', $string);
        $string = str_replace(' sgt ', ' ', $string);
        $string = str_replace(' sargento ', ' ', $string);
        $string = str_replace(' capitao ', ' ', $string);
        
        $string = str_replace(' carroceiro ', ' ', $string);
        $string = str_replace(' carroceira ', ' ', $string);
        $string = str_replace(' contador ', ' ', $string);
        $string = str_replace(' contadora ', ' ', $string);
        $string = str_replace(' emfermeiro ', ' ', $string);
        $string = str_replace(' enfermeira ', ' ', $string);
        $string = str_replace(' sindicato ', ' ', $string);
        $string = str_replace(' pedreiro ', ' ', $string);
        $string = str_replace(' cabelereiro ', ' ', $string);
        $string = str_replace(' cabelereira ', ' ', $string);
        $string = str_replace(' fotografo ', ' ', $string);
        $string = str_replace(' pescador ', ' ', $string);
        $string = str_replace(' pescadores ', ' ', $string);
        
        $string = str_replace(' a ', ' ', $string);
        $string = str_replace(' e ', ' ', $string);
        $string = str_replace(' i ', ' ', $string);
        $string = str_replace(' o ', ' ', $string);
        $string = str_replace(' u ', ' ', $string);
        $string = str_replace(' da ', ' ', $string);
        $string = str_replace(' de ', ' ', $string);
        $string = str_replace(' do ', ' ', $string);
        $string = str_replace(' das ', ' ', $string);
        $string = str_replace(' dos ', ' ', $string);
        
        $stParts = explode(' ', $string);
        $string = '';
        foreach ($stParts as $value) {
            if(strlen($value) > 1){
                $string = $string . ' ' . $value;
            }
        }
        
        $string = preg_replace('/\s+|\s+/', ' ', $string);
        $string = trim($string);
        return $string;
    }
    
    public function indexAction() {

        ############################################################################################
        ############################################################################################
        
        $tempo = time();
        $this->inicilizaLoop();
        
        $db = Zend_Registry::get('db');
        $array = $db->fetchAll('SELECT * FROM pu_pessoa LIMIT ' . (($this->view->contador-1)*1000) . ', 1000');
        if(count($array) > 1){
            foreach ($array as $p) {
                if($p['pessoa_flags'] > 0){
                    $dir = Default_Model_Diretorio::getDiretorioPessoa($p['pessoa_id']);
                    $data = getimagesize(PUBLIC_PATH . $dir . '/foto.jpg');
                    $data = array(
                        'pessoa_id'   => $p['pessoa_id'],
                        'imagem_tipo' => $data['mime'],
                        'imagem_data' => file_get_contents(PUBLIC_PATH . $dir . '/foto.jpg')
                    );
                    try {
                        $db->insert('pu_pessoa_imagem', $data);
                    } catch (Exception $exc) {
                        
                    }
                }
            }
        }else{
            $this->finalizaLoop();
        }
        Alex_Util::debugEscreve('QTD PESSOAS.........: ' . (($this->view->contador-1)*1000));
        Alex_Util::debugEscreve('QTD SEGUNDOS........: ' . (time()-$tempo));

        ############################################################################################
        ############################################################################################
//        $tempo = time();
//        $this->inicilizaLoop();
//        $db = Zend_Registry::get('db');
//        $tmp = $db->fetchAll('
//            SELECT I.pessoa_id
//            FROM pu_pessoa_termo_indice I LEFT JOIN pu_pessoa P
//            ON I.pessoa_id=P.pessoa_id
//            WHERE P.pessoa_id IS NULL
//            LIMIT 0, 100
//        ');
//        if(count($tmp) > 1){
//            $ids = array();
//            foreach ($tmp as $value) {
//                $ids[] = $value['pessoa_id'];
//            }
//            $db->delete('pu_pessoa_termo_indice', 'pessoa_id IN (' . implode(', ', $ids) . ')');
//        }else{
//            $this->finalizaLoop();
//        }
//        Alex_Util::debugEscreve('QTD PESSOAS.........: ' . count($tmp));
//        Alex_Util::debugEscreve('QTD SEGUNDOS........: ' . (time()-$tempo));
//        Alex_Util::debugEscreve('QTD SEGUNDOS/PESSOA.: ' . ((time()-$tempo)/count($tmp)));
        ############################################################################################
        ############################################################################################
        
//        $qtdTotal = $qtdDiferente = $qtdIgual = $qtdNomeDiferente = $qtdApelidoDiferente = $qtdApelidoZero = 0;
//        $tempo = time();
//        $this->inicilizaLoop();
//        $db = Zend_Registry::get('db');
//        $titulos = $db->fetchAll('
//            SELECT pessoa_titulo_eleitor
//            FROM lista_duplicados_titulos
//            ORDER BY pessoa_titulo_eleitor
//            LIMIT ' . (($this->view->contador-1)*1000) . ', 1000
//        ');
//        if(count($titulos) > 0){
//            $qtdTotal+= count($titulos);
//            foreach ($titulos as $t) {
//                $pessoas = $db->fetchAll('SELECT * FROM pu_pessoa WHERE pessoa_titulo_eleitor=' . $t['pessoa_titulo_eleitor'] . ' ORDER BY pessoa_id');
//                if(count($pessoas) > 1){
//                    $principal = $pessoas[0];
//                    $principal['pessoa_nome'] = $this->simplificaNome($principal['pessoa_nome']);
//                    $principal['pessoa_apelido'] = $this->simplificaNome($principal['pessoa_apelido']);
//                    unset($pessoas[0]);
//
//                    foreach ($pessoas as $value) {
//                        $igual = true;
//                        
//                        // VALIDAÇÃO DO NOME //
//                        $value['pessoa_nome'] = $this->simplificaNome($value['pessoa_nome']);
//                        if($value['pessoa_nome'] != $principal['pessoa_nome']){
//                            $igual = false;
//                            
//                            $vNmParts = explode(' ', $value['pessoa_nome']);
//                            if(count($vNmParts) > 0){
//                                $qtdNms = 0;
//                                $pNmParts = explode(' ', $principal['pessoa_nome']);
//                                foreach ($vNmParts as $k => $vNm) {
//                                    foreach ($pNmParts as $x => $pNm) {
//                                        if($vNm == $pNm){
//                                            $qtdNms++;
//                                            unset($vNmParts[$k]);
//                                            unset($pNmParts[$x]);
//                                            break;
//                                        }
//                                    }
//                                }
//                                if($qtdNms > 1){
//                                    if(count($vNmParts) < 2 && count($pNmParts) < 2){
//                                        $igual = true;
////                                        Alex_Util::debugEscreve($principal['pessoa_titulo_eleitor']);
////                                        Alex_Util::debugEscreve('NMS.......: ' . $qtdNms);
////                                        Alex_Util::debugEscreve('PRINC.....: ' . $principal['pessoa_nome'] . ' - ' . $principal['pessoa_apelido']);
////                                        Alex_Util::debugEscreve('VALUE.....: ' . $value['pessoa_nome'] . ' - ' . $value['pessoa_apelido']);
////                                        Alex_Util::debugEscreve($vNmParts);
////                                        Alex_Util::debugEscreve($pNmParts);
////                                        Alex_Util::debugEscreve('<hr/>');
//                                    }
//                                }
//                            }
//                            if(!$igual){
//                                $qtdNomeDiferente++;
//                            }
//                        }
//                        
//                        // VALIDAÇÃO DO APELIDO //
//                        if($igual){
//                            if(strlen($value['pessoa_apelido']) > 1){
//                                $value['pessoa_apelido'] = $this->simplificaNome($value['pessoa_apelido']);
//                                if($value['pessoa_apelido'] != $principal['pessoa_apelido'] && $value['pessoa_apelido'] != $principal['pessoa_nome']){
//                                    $igual = false;
//
//                                    $vNmParts = explode(' ', $value['pessoa_apelido'] . ' ' . $principal['pessoa_apelido']);
//                                    if(count($vNmParts) == 1){
//                                        $pNmParts = explode(' ', $principal['pessoa_nome']);
//                                        foreach ($pNmParts as $pNm) {
//                                            if($pNm == $vNmParts[0]){
//                                                $igual = true;
//                                                break;
//                                            }
//                                        }
//                                    }elseif(count($vNmParts) > 1){
//                                        $pNmParts = explode(' ', $principal['pessoa_nome'] . ' ' . $principal['pessoa_apelido']); // 
//                                        foreach ($vNmParts as $k => $vNm) {
//                                            foreach ($pNmParts as $pNm) {
//                                                if($vNm == $pNm){
//                                                    unset($vNmParts[$k]);
//                                                    break;
//                                                }
//                                            }
//                                        }
//                                        if(!(count($vNmParts) > 0)){
//                                            $igual = true;
//                                        }
//                                    }
//
//                                    if(!$igual){
//                                        $qtdApelidoDiferente++;
////                                        Alex_Util::debugEscreve($principal['pessoa_titulo_eleitor']);
////                                        Alex_Util::debugEscreve('PRINC.....: ' . $principal['pessoa_nome'] . ' - ' . $principal['pessoa_apelido']);
////                                        Alex_Util::debugEscreve('VALUE.....: ' . $value['pessoa_apelido']);
////                                        Alex_Util::debugEscreve('<hr/>');
//                                    }
//                                }
//                            }else{
//                                $qtdApelidoZero++;
//                            }
//                        }
//                        
//                        if($igual){
//                            // SALVA FOTO //
//                            $principal_dir = Default_Model_Diretorio::getDiretorioPessoa($principal['pessoa_id']);
//                            if(!file_exists(PUBLIC_PATH . '/uploads/pessoas' . $principal_dir . '/foto.jpg')){
//                                $dir = Default_Model_Diretorio::getDiretorioPessoa($value['pessoa_id']);
//                                if(file_exists(PUBLIC_PATH . '/uploads/pessoas' . $dir . '/foto.jpg')){
//                                    copy(PUBLIC_PATH . '/uploads/pessoas' . $dir . '/foto.jpg',
//                                         PUBLIC_PATH . '/uploads/pessoas' . $principal_dir . '/foto.jpg'
//                                    );
//                                    $db->update('pu_pessoa', array('pessoa_flags' => '10000'), 'pessoa_id=' . $principal['pessoa_id']);
//                                }
//                            }
//
//                            // UPDATE //
//                            $db->update('pu_candidatura'    , array('pessoa_id' => $principal['pessoa_id']), 'pessoa_id=' . $value['pessoa_id']);
//                            $db->update('pu_bem'            , array('pessoa_id' => $principal['pessoa_id']), 'pessoa_id=' . $value['pessoa_id']);
//                            
//                            // DELETE //
//                            $db->delete('pu_pessoa_detalhe' , 'pessoa_id=' . $value['pessoa_id']);
//                            $db->delete('pu_pessoa'         , 'pessoa_id=' . $value['pessoa_id']);
//
//                            // DIRETÓRIO //
//                            $this->excluiPasta(PUBLIC_PATH . '/uploads/pessoas' . Default_Model_Diretorio::getDiretorioPessoa($value['pessoa_id']));
//                            $qtdIgual++;
//                        }else{
//                            $qtdDiferente++;
//                        }
//                    }
//                }
//            }
//            Alex_Util::debugEscreve('<hr/>');
//            Alex_Util::debugEscreve('QTD NOME DIFERENTE.................: ' . $qtdNomeDiferente);
//            Alex_Util::debugEscreve('QTD APELIDO DIFERENTE..............: ' . $qtdApelidoDiferente);
//            Alex_Util::debugEscreve('QTD APELIDO ZERO...................: ' . $qtdApelidoZero);
//            Alex_Util::debugEscreve('QTD DIFERENTE.......: ' . $qtdDiferente);
//            Alex_Util::debugEscreve('QTD IGUAL...........: ' . $qtdIgual);
//            Alex_Util::debugEscreve('QTD TOTAL...........: ' . ($qtdDiferente+$qtdIgual));
//            Alex_Util::debugEscreve('QTD SEGUNDOS........: ' . (time()-$tempo));
//        }else{
//            $this->finalizaLoop();
//        }
        
        ############################################################################################
        ############################################################################################
        
        //$this->criaPastas(100000);
        //$this->calculaImagens();
        
//        $origem = 'D:/Studio/Sites/Alex/o-povo-unido-data/FOTOS/2006';
//        $ufs = Default_Model_Lista_Estado::get();
//        foreach ($ufs as $ufId => $ufDt) {
//            if(file_exists($origem . '/' . $ufDt[0])){
////                // REMOVE ARQUIVOS INUTEIS //
////                $arqs = opendir($origem . '/' . $ufDt[0]);
////                while ($arq = readdir($arqs)) {
////                    if(substr($arq, -4) == '.htm') {
////                        $caminho = $origem . '/' . $ufDt[0] . '/' .$arq;
////                        unlink($caminho);
////                        Alex_Util::debugEscreve($arq);
////                    }
////                }
//                
////                // RENOMEA ARQUIVOS //
////                if(file_exists($origem . '/' . $ufDt[0] . '.htm')){
////                    $f = fopen($origem . '/' . $ufDt[0] . '.htm', 'r');
////                    while ($linha = fgets($f)) {
////                        if(substr($linha, 1, 4) == 'img '){
////                            $tmp = explode(' ', substr($linha, 1));
////                            $old = $origem . '/' . $ufDt[0] . '/' . substr($tmp[1], 17, -1);
////                            $new = $origem . '/' . $ufDt[0] . '/' . substr($tmp[2], 7, -4);
////                            
////                            //Alex_Util::debugEscreve($old);
////                            //Alex_Util::debugEscreve($new);
////                            
////                            if(file_exists($old)){ // !falhas
////                                if(substr($old, -4) == '.jpg') {
////                                    rename($old, $origem . '/!falhas/' . substr($tmp[2], 7, -4));
////                                }else{
////                                    rename($old, $new);
////                                }
////                            }else{
////                                Alex_Util::debugEscreve('ARQUIVO NAO EXISTE: ' . basename($old));
////                            }
////                        }
////                    }
////                }
//            }
//        }
    }
    
    public function calculaImagens() {
        $this->inicilizaLoop();
        if($this->view->contador < 1){
            return;
        }
        
        $tempo = time();
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('SELECT pessoa_id FROM pu_pessoa WHERE pessoa_status=1 LIMIT 0, 5000');
        if(count($res) > 0){
            $idsSim = $idsNao = array();
            $destino = PUBLIC_PATH . '/uploads/pessoas';
            foreach ($res as $value) {
                $foto = $destino . Default_Model_Diretorio::getDiretorioPessoa($value['pessoa_id']) . '/foto.jpg';
                if(file_exists($foto)){
                    $idsSim[] = $value['pessoa_id'];
                }else{
                    $idsNao[] = $value['pessoa_id'];
                }
            }
            
            if(count($idsSim) > 0){
                $db->update('pu_pessoa', array('pessoa_flags' => 10000, 'pessoa_status' => 0), 'pessoa_id IN (' . implode(', ', $idsSim) . ') ');
            }
            if(count($idsNao) > 0){
                $db->update('pu_pessoa', array('pessoa_flags' => 00000, 'pessoa_status' => 0), 'pessoa_id IN (' . implode(', ', $idsNao) . ') ');
            }
            
            Alex_Util::debugEscreve('QTD SIM.............: ' . count($idsSim));
            Alex_Util::debugEscreve('QTD NAO.............: ' . count($idsNao));
            Alex_Util::debugEscreve('TEMPO...............: ' . (time() - $tempo) . ' segundos');
        }else{
            $this->finalizaLoop();
        }
    }
    
    public function paginaAction() {
        $anos = array(
            // MAJORITARIAS //
            '2010', // OK
            '2006', // OK
            '2002', // OK
          //'1998', // não tem
          //'1994', // não tem
            
            // MUNICIPAIS //
            '2012', // baixando
            '2008', // baixando
            '2004', // baixando
          //'2000', // não tem
          //'1996'  // não tem
        );
        
        echo '<hr><table style="text-align:center;width:100%">';
        $ufs = Default_Model_Lista_Estado::get();
        foreach ($ufs as $ufid => $uf) {
            if($ufid > 0){
                echo '<tr>';
                foreach ($anos as $ano) {
                        echo '<td><a target="_blank" href="/extracao/index/pagina-extracao/ANO/' . $ano . '/UF/' . $uf[0] . '/UFID/' . $ufid . '">' . $ano . ' - ' . $uf[0] . '</a></td>';
                }
                echo '</tr>';
            }
        }
        echo '</table>';
    }
    
    public function paginaExtracaoAction() {
        $get = $this->getRequest()->getParams();
        if(isset($get['ANO']) && isset($get['UF']) && isset($get['UFID']) && $get['ANO'] > 0 && $get['UFID'] > 0 && strlen($get['UF']) == 2){
            $this->view->ano = $get['ANO'];
            $this->view->ufid = $get['UFID'];
            $this->view->uf = $get['UF'];
            
            switch ($get['ANO']){
                // MAJORITARIAS //
                case 2010: $this->extrairFotos2010($get['UFID']); break;
                case 2006: $this->extrairFotos2006($get['UFID']); break;
                case 2002: $this->extrairFotos2002($get['UFID']); break;
                case 1998: $this->extrairFotos1998($get['UFID']); break;
                case 1994: $this->extrairFotos1994($get['UFID']); break;

                // MUNICIPAIS //
                case 2012: $this->extrairFotos2012($get['UFID']); break;
                case 2008: $this->extrairFotos2008($get['UFID']); break;
                case 2004: $this->extrairFotos2004($get['UFID']); break;
                case 2000: $this->extrairFotos2000($get['UFID']); break;
                case 1996: $this->extrairFotos1996($get['UFID']); break;
            }
        }
    }

    public function relacionaFotosAction() {
        $get = $this->getRequest()->getParams();
        if(isset($get['ANO']) && $get['ANO'] > 0){
            $this->view->ano = $get['ANO'];
            
            switch ($get['ANO']){
                // MAJORITARIAS //
                //case 2010: $this->relacionaFotos2010(); break; // ok
                //case 2006: $this->relacionaFotos2006(); break; // ok
                //case 2002: $this->relacionaFotos2002(); break; // ok
                //case 1998: $this->relacionaFotos1998(); break; // não tem
                //case 1994: $this->relacionaFotos1994(); break; // não tem

                // MUNICIPAIS //
                //case 2012: $this->relacionaFotos2012(); break;
                //case 2008: $this->relacionaFotos2008(); break;
                //case 2004: $this->relacionaFotos2004(); break;
                //case 2000: $this->relacionaFotos2000(); break; // não tem
                //case 1996: $this->relacionaFotos1996(); break; // não tem
            }
        }
    }
    
    // RELACIONA FOTOS MUNICIPAIS //
    
    public function relacionaFotos2012($qtd=500) {
        $this->inicilizaLoop();
        if($this->view->contador < 1){
            return;
        }
        
        $tempo = time();
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT C.pessoa_id, C.cand_estado_id, C.cand_municipio_id, D.cand_pessoa_cd_tse
            FROM pu_candidatura C, pu_candidatura_detalhe D, pu_pessoa P
            WHERE C.eleicao_id=2012
               && P.pessoa_status=0
               && C.cand_id=D.cand_id
               && C.pessoa_id=P.pessoa_id
            LIMIT 0, ' . $qtd
        );
          //LIMIT ' . (($this->view->contador-1)*$qtd) . ', ' . $qtd

        if(count($res) > 0){
            $origem = 'D:/Studio/Sites/Alex/o-povo-unido-data/FOTOS/2012';
            $destino = PUBLIC_PATH . '/uploads/pessoas';
            
            $ufs = Default_Model_Lista_Estado::get();
            $sim = $nao = $newFoto = 0;
            $ids = array();
            foreach ($res as $value) {
                $foto = $origem . '/' . $ufs[$value['cand_estado_id']][0] . '/F' . $ufs[$value['cand_estado_id']][0] . $value['cand_pessoa_cd_tse'] . '.jpg';
                if(file_exists($foto)){
                    $sim++;
                    $pasta = $destino . Default_Model_Diretorio::getDiretorioPessoa($value['pessoa_id']);
                    try {
                        copy($foto, $pasta . '/foto.jpg');
                        $newFoto++;
                    } catch (Exception $exc) {
                        echo $exc->getTraceAsString();
                    }
                }else{
                    $nao++;
                }
                $ids[] = $value['pessoa_id'];
            }
            
            if(count($ids) > 0){
                $db->update('pu_pessoa', array('pessoa_flags' => 10000, 'pessoa_status' => 1), 'pessoa_id IN (' . implode(', ', $ids) . ') ');
            }
            
            Alex_Util::debugEscreve('QTD ECONTRADAS......: ' . $sim);
            Alex_Util::debugEscreve('QTD Ñ ENCONTRADAS...: ' . $nao);
            Alex_Util::debugEscreve('QTD NOVAS FOTOS.....: ' . $newFoto);
            Alex_Util::debugEscreve('ÚLTIMO REGISTRO.....: ');
            Alex_Util::debugEscreve($value);
            Alex_Util::debugEscreve('TEMPO...............: ' . (time() - $tempo) . ' segundos');
        }else{
            $this->finalizaLoop();
        }
    }
    
    // RELACIONA FOTOS MAJORITÁRIAS //
    
    public function relacionaFotos2010($qtd=500) {
        $this->inicilizaLoop();
        if($this->view->contador < 1){
            return;
        }
        
        $tempo = time();
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT C.pessoa_id, C.cand_estado_id, D.cand_pessoa_cd_tse
            FROM pu_candidatura C, pu_candidatura_detalhe D, pu_pessoa P
            WHERE C.eleicao_id=2010
               && P.pessoa_status=0
               && C.cand_id=D.cand_id
               && C.pessoa_id=P.pessoa_id
            LIMIT 0, ' . $qtd
        );
          //LIMIT ' . (($this->view->contador-1)*$qtd) . ', ' . $qtd

        if(count($res) > 0){
            $origem = 'D:/Studio/Sites/Alex/o-povo-unido-data/FOTOS/2010';
            $destino = PUBLIC_PATH . '/uploads/pessoas';
            
            $ufs = Default_Model_Lista_Estado::get();
            $sim = $nao = $newFoto = 0;
            $ids = array();
            foreach ($res as $value) {
                $foto = $origem . '/' . $ufs[$value['cand_estado_id']][0] . '/F' . $ufs[$value['cand_estado_id']][0] . $value['cand_pessoa_cd_tse'] . '.jpg';
                if(file_exists($foto)){
                    $sim++;
                    $pasta = $destino . Default_Model_Diretorio::getDiretorioPessoa($value['pessoa_id']);
                    try {
                        copy($foto, $pasta . '/foto.jpg');
                        $newFoto++;
                    } catch (Exception $exc) {
                        echo $exc->getTraceAsString();
                    }
                }else{
                    $nao++;
                }
                $ids[] = $value['pessoa_id'];
            }
            
            if(count($ids) > 0){
                $db->update('pu_pessoa', array('pessoa_flags' => 10000, 'pessoa_status' => 1), 'pessoa_id IN (' . implode(', ', $ids) . ') ');
            }
            
            Alex_Util::debugEscreve('QTD ECONTRADAS......: ' . $sim);
            Alex_Util::debugEscreve('QTD Ñ ENCONTRADAS...: ' . $nao);
            Alex_Util::debugEscreve('QTD NOVAS FOTOS.....: ' . $newFoto);
            Alex_Util::debugEscreve('ÚLTIMO REGISTRO.....: ');
            Alex_Util::debugEscreve($value);
            Alex_Util::debugEscreve('TEMPO...............: ' . (time() - $tempo) . ' segundos');
        }else{
            $this->finalizaLoop();
        }
    }
    
    public function relacionaFotos2006($qtd=500) {
        $this->inicilizaLoop();
        if($this->view->contador < 1){
            return;
        }
        
        $tempo = time();
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT C.pessoa_id, C.cand_estado_id, D.cand_pessoa_cd_tse
            FROM pu_candidatura C, pu_candidatura_detalhe D, pu_pessoa P
            WHERE C.eleicao_id=2006
               && P.pessoa_status=0
               && C.cand_id=D.cand_id
               && C.pessoa_id=P.pessoa_id
            LIMIT 0, ' . $qtd
        );

        if(count($res) > 0){
            $origem = 'D:/Studio/Sites/Alex/o-povo-unido-data/FOTOS/2006';
            $destino = PUBLIC_PATH . '/uploads/pessoas';
            
            $ufs = Default_Model_Lista_Estado::get();
            $sim = $nao = $newFoto = 0;
            $ids = array();
            foreach ($res as $value) {
                $foto = $origem . '/' . $ufs[$value['cand_estado_id']][0] . '/F' . $ufs[$value['cand_estado_id']][0] . '-'. $value['cand_pessoa_cd_tse'] . '.jpg';
                if(file_exists($foto)){
                    $sim++;
                    $pasta = $destino . Default_Model_Diretorio::getDiretorioPessoa($value['pessoa_id']);
                    try {
                        copy($foto, $pasta . '/foto.jpg');
                        $newFoto++;
                    } catch (Exception $exc) {
                        echo $exc->getTraceAsString();
                    }
                }else{
                    $nao++;
                }
                $ids[] = $value['pessoa_id'];
            }
            
            if(count($ids) > 0){
                $db->update('pu_pessoa', array('pessoa_flags' => 10000, 'pessoa_status' => 1), 'pessoa_id IN (' . implode(', ', $ids) . ') ');
            }
            
            Alex_Util::debugEscreve('QTD ECONTRADAS......: ' . $sim);
            Alex_Util::debugEscreve('QTD Ñ ENCONTRADAS...: ' . $nao);
            Alex_Util::debugEscreve('QTD NOVAS FOTOS.....: ' . $newFoto);
            Alex_Util::debugEscreve('ÚLTIMO REGISTRO.....: ');
            Alex_Util::debugEscreve($value);
            Alex_Util::debugEscreve('TEMPO...............: ' . (time() - $tempo) . ' segundos');
        }else{
            $this->finalizaLoop();
        }
    }
    
    public function relacionaFotos2002($qtd=500) {
        $this->inicilizaLoop();
        if($this->view->contador < 1){
            return;
        }
        
        $tempo = time();
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT C.pessoa_id, C.cand_estado_id, D.cand_pessoa_cd_tse
            FROM pu_candidatura C, pu_candidatura_detalhe D, pu_pessoa P
            WHERE C.eleicao_id=2002
               && P.pessoa_status=0
               && C.cand_id=D.cand_id
               && C.pessoa_id=P.pessoa_id
            LIMIT 0, ' . $qtd
        );
          //LIMIT ' . (($this->view->contador-1)*$qtd) . ', ' . $qtd

        if(count($res) > 0){
            $origem = 'D:/Studio/Sites/Alex/o-povo-unido-data/FOTOS/2002';
            $destino = PUBLIC_PATH . '/uploads/pessoas';
            
            $ufs = Default_Model_Lista_Estado::get();
            $sim = $nao = $newFoto = 0;
            $ids = array();
            foreach ($res as $value) {
                $foto = $origem . '/' . $ufs[$value['cand_estado_id']][0] . '/F' . $ufs[$value['cand_estado_id']][0] . '-'. $value['cand_pessoa_cd_tse'] . '.jpg';
                if(file_exists($foto)){
                    $sim++;
                    $pasta = $destino . Default_Model_Diretorio::getDiretorioPessoa($value['pessoa_id']);
                    try {
                        copy($foto, $pasta . '/foto.jpg');
                        $newFoto++;
                    } catch (Exception $exc) {
                        echo $exc->getTraceAsString();
                    }
                }else{
                    $nao++;
                }
                $ids[] = $value['pessoa_id'];
            }
            
            if(count($ids) > 0){
                $db->update('pu_pessoa', array('pessoa_flags' => 10000, 'pessoa_status' => 1), 'pessoa_id IN (' . implode(', ', $ids) . ') ');
            }
            
            Alex_Util::debugEscreve('QTD ECONTRADAS......: ' . $sim);
            Alex_Util::debugEscreve('QTD Ñ ENCONTRADAS...: ' . $nao);
            Alex_Util::debugEscreve('QTD NOVAS FOTOS.....: ' . $newFoto);
            Alex_Util::debugEscreve('ÚLTIMO REGISTRO.....: ');
            Alex_Util::debugEscreve($value);
            Alex_Util::debugEscreve('TEMPO...............: ' . (time() - $tempo) . ' segundos');
        }else{
            $this->finalizaLoop();
        }
    }
    
    // MÁTODOS //
    
    public function criaPastas($qtd=1000) {
        $this->inicilizaLoop();
        if($this->view->contador > 0){
            $ok = $dup = 0;
            $ini = ($this->view->contador-1)*$qtd;
            $fim = $ini+$qtd;
            $tempo = time();
            $res = Zend_Registry::get('db')->fetchRow('SELECT MAX(pessoa_id) AS qtd FROM pu_pessoa');
            $pasta = '';
            
            for($id=$ini; $id<=$fim; $id++){
                if($id > $res['qtd']){
                    $this->finalizaLoop();
                    return;
                }
                $pasta = '/uploads/pessoas' . Default_Model_Diretorio::getDiretorioPessoa($id);
                if(!file_exists(PUBLIC_PATH . $pasta)){
                    try {
                        mkdir(PUBLIC_PATH . $pasta, 0744, true);
                        $ok++;
                    } catch (Exception $exc) {
                        Alex_Util::debugEscreve('ERRRO AO TENTAR CRIAR O DIRETÓRIO: ' . $pasta);
                        echo $exc->getTraceAsString();
                        exit;
                    }
                }else{
                    $dup++;
                }
            }
            
            Alex_Util::debugEscreve('QTD INI.............: ' . $ini);
            Alex_Util::debugEscreve('QTD FIM.............: ' . $fim);
            Alex_Util::debugEscreve('QTD CRIADAS.........: ' . $ok);
            Alex_Util::debugEscreve('QTD DUPLICADAS......: ' . $dup);
            Alex_Util::debugEscreve('QTD BANCO...........: ' . $res['qtd']);
            Alex_Util::debugEscreve('TEMPO...............: ' . (time() - $tempo) . ' segundos');
            Alex_Util::debugEscreve('ÚLTIMA PASTA CRIADA.: ' . $pasta);
            $this->finalizaLoop();            
        }
    }
    
    public function normalizaNomesPessoas($qtd=1000) {
        Alex_Util::debugEscreve('É MESMO NECESSÁRIO ?');
        return;
        
        $this->inicilizaLoop();
        if($this->view->contador > 0){
            $tempo = time();
            $db = Zend_Registry::get('db');
            $res = $db->fetchall('
                SELECT pessoa_id, pessoa_nome, pessoa_apelido
                FROM pu_pessoa
                WHERE pessoa_status=0
                LIMIT 0, ' . $qtd
            );
            if(count($res) > 0){
                foreach ($res as $value) {
//                    $db->update('pu_pessoa', array(
//                        'pessoa_status' => 1,
//                        'pessoa_nome' => $this->trataNome($value['pessoa_nome']),
//                        'pessoa_apelido' => $this->trataNome($value['pessoa_apelido']),
//                    ), 'pessoa_id=' . $value['pessoa_id']);
                }
            }else{
                $this->finalizaLoop();
            }
            Alex_Util::debugEscreve('TEMPO...........: ' . (time() - $tempo));
        }
    }
    
    public function normalizaNomesCandidaturas($qtd=1000) {
        Alex_Util::debugEscreve('É MESMO NECESSÁRIO ?');
        return;
        
        $this->inicilizaLoop();
        if($this->view->contador > 0){
            $tempo = time();
            $db = Zend_Registry::get('db');
            $res = $db->fetchall('
                SELECT cand_id, cand_urna_nome
                FROM pu_candidatura
                WHERE cand_status=0
                LIMIT 0, ' . $qtd
            );
            if(count($res) > 0){
                foreach ($res as $value) {
//                    $db->update('pu_candidatura', array(
//                        'cand_status' => 1,
//                        'cand_urna_nome' => $this->trataNome($value['cand_urna_nome']),
//                    ), 'cand_id=' . $value['cand_id']);
                }
            }else{
            }
                $this->finalizaLoop();
            Alex_Util::debugEscreve('TEMPO...........: ' . (time() - $tempo));
        }
    }
    
    // EXTRAÇÃO DE FOTOS //
    
    public function extrairFotos($linkOld, $linkNew) {
        $old = @fopen($linkOld, 'r');
        if($old){
            $pasta = dirname($linkNew);
            if (!file_exists($pasta)) {
                mkdir($pasta, 0744, true);
            }
            $new = fopen($linkNew, 'w');
            while ($linha = fgets($old)) {
                fwrite($new, $linha);
            }
            fclose($new);
            fclose($old);
            return true;
        }else{
            Alex_Util::debugEscreve('NÃO ENCONTRADO......: <a href="' . $linkOld . '">' . basename($linkOld) . '</a>');
            return false;
        }
    }
    
    // MAJORITARIAS //
    
    public function extrairFotos2010($uf_id=1) {
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT C.cand_estado_id AS uf, D.cand_pessoa_cd_tse AS codigo
            FROM pu_candidatura C, pu_candidatura_detalhe D
            WHERE eleicao_id=2010
               && C.cand_estado_id=' . $uf_id . '
               && C.cand_id=D.cand_id
        ');
        if(count($res) > 0){
            $origem = 'http://el.imguol.com/2010/fichas';
            $uf = Default_Model_Lista_Estado::get($uf_id);
            Alex_Util::debugEscreve($uf[0] . ' (' . $uf_id . ')....: ' . count($res));
            foreach ($res as $value) {
                echo "\n" . '<img src="' . $origem . '/' . $uf[0] . '/F' . $uf[0] . $value['codigo'] . '.jpg" title="F' . $uf[0] . $value['codigo'] . '.jpg">';
            }
            echo "\n";
        }
    }
    
    public function extrairFotos2006($uf_id=1){
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT C.cand_estado_id AS uf, D.cand_pessoa_cd_tse AS codigo
            FROM pu_candidatura C, pu_candidatura_detalhe D
            WHERE C.eleicao_id=2006
               && C.cand_estado_id=' . $uf_id .'
               && C.cand_id=D.cand_id
        ');
        if(count($res) > 0){
//            $origem = 'http://n.i.uol.com.br/fernandorodrigues/politicos/candidatos/fotos/2006';
//            $uf = Default_Model_Lista_Estado::get($uf_id);
//            Alex_Util::debugEscreve($uf[0] . ' (' . $uf_id . ')....: ' . count($res));
//            foreach ($res as $value) {
//                echo "\n" . '<img src="' . $origem . '/' . $uf[0] . '/' . $value['cargo'] . '-' . $value['numero'] . '.jpg" title="F' .  $uf[0] . '-' . $value['cargo'] . '-' . $value['numero'] . '.jpg">';
//            }
//            echo "\n";
            
            $origem = 'http://www.tse.jus.br/sadEleicao2006DivCand/candidatoFoto.jsp?sq_cand=';
            $uf = Default_Model_Lista_Estado::get($uf_id);
            Alex_Util::debugEscreve($uf[0] . ' (' . $uf_id . ')....: ' . count($res));
            foreach ($res as $value) {
                echo "\n" . '<img src="' . $origem . $value['codigo'] . '&sg_ue=' .  $uf[0] . '&temp=oracle.sql.BLOB@275b85" title="F' .  $uf[0] . '-' . $value['codigo'] . '.jpg">';
            }
            echo "\n";
        }
    }
    
    public function extrairFotos2002($uf_id=1){
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT D.cand_pessoa_cd_tse AS codigo
            FROM pu_candidatura C, pu_candidatura_detalhe D
            WHERE C.eleicao_id=2002
               && C.cand_estado_id=' . $uf_id . '
               && C.cand_id=D.cand_id
        ');
        if(count($res) > 0){
            $origem = 'http://www.tse.jus.br/sadEleicao2002/divulgacaoDeCandidatos/candidatoFoto.jsp?sq_cand=';
            $uf = Default_Model_Lista_Estado::get($uf_id);
            Alex_Util::debugEscreve($uf[0] . ' (' . $uf_id . ')....: ' . count($res));
            foreach ($res as $value) {
                echo "\n" . '<img src="' . $origem . $value['codigo'] . '&amp;sg_ue=' .  $uf[0] . '" title="F' .  $uf[0] . '-' . $value['codigo'] . '.jpg">';
            }
            echo "\n";
        }
    }
    
    public function extrairFotos1998($uf_id=1){
        Alex_Util::debugEscreve('NÃO IMPLEMENTADO');
    }
    
    public function extrairFotos1994($uf_id=1){
        Alex_Util::debugEscreve('NÃO IMPLEMENTADO');
    }
    
    // MUNICIPAIS //
    
    public function extrairFotos2012($uf_id=1) {
        $this->inicilizaLoop();
        if($this->view->contador > 0){
            return;
        }
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT C.cand_estado_id AS uf, D.cand_pessoa_cd_tse AS codigo
            FROM pu_candidatura C, pu_candidatura_detalhe D
            WHERE eleicao_id=2012
               && C.cand_id=D.cand_id
               && C.cand_estado_id=' . $uf_id . '
            LIMIT ' . (($this->view->contador-1)*$this->pagina) . ', ' . $this->pagina
        );
        if(count($res) > 0){
            $origem = 'http://el.imguol.com/2012/fichas';
            $uf = Default_Model_Lista_Estado::get($uf_id);
            Alex_Util::debugEscreve($uf[0] . ' (' . $uf_id . ')..............: ' . count($res));
            $ok = $erro = 0;
            foreach ($res as $value) {
                if($this->extrairFotos(
                    $origem . '/' . $uf[0] . '/F' . $uf[0] . $value['codigo'] . '.jpg',
                    $this->destino . '/2012/' . $uf[0] . '/F' . $uf[0] . $value['codigo'] . '.jpg'
                )){
                    $ok++;
                }else{
                    $erro++;
                }
            }
            Alex_Util::debugEscreve('COPIADAS............: ' . $ok);
            Alex_Util::debugEscreve('NÃO ENCONTRADAS.....: ' . $erro);
        }else{
            $this->finalizaLoop();
        }
    }
    
    public function extrairFotos2008($uf_id=1){
        $this->inicilizaLoop();
        if($this->view->contador > 0){
            return;
        }
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT C.cand_estado_id AS uf, C.cand_municipio_id AS municipio, D.cand_pessoa_cd_tse AS codigo
            FROM pu_candidatura C, pu_candidatura_detalhe D
            WHERE C.eleicao_id=2008
               && C.cand_id=D.cand_id
               && C.cand_status=0
            LIMIT ' . (($this->view->contador-1)*$this->pagina) . ', ' . $this->pagina
        );
        if(count($res) > 0){
            $municipios = $tmp = array();
            foreach ($res as $value) {
                $tmp[$value['municipio']] = $value['municipio'];
            }
            
            $tmp = $db->fetchAll('SELECT municipio_id, municipio_cd_tse FROM pu_municipio WHERE municipio_id IN (' . implode(', ', $tmp) . ')');
            foreach ($tmp as $value) {
                $municipios[$value['municipio_id']] = str_pad($value['municipio_cd_tse'], 5, '0', STR_PAD_LEFT);
            }
            unset($tmp);
            
            $origem = 'http://www.tse.jus.br/sadEleicaoDivulgaCand2008/comuns/imagens/fotosCandidatosTemp';
            $uf = Default_Model_Lista_Estado::get($uf_id);
            Alex_Util::debugEscreve($uf[0] . ' (' . $uf_id . ')..............: ' . count($res));
            $ok = $erro = 0;
            foreach ($res as $value) {
                if($this->extrairFotos(
                    $origem . '/FotoCandidato-' . $municipios[$value['municipio']] . '-' . $value['codigo'] . '.jpg',
                    $this->destino . '/2008/' . $uf[0] . '/F' .  $uf[0] . '-' . $municipios[$value['municipio']] . '-' . $value['codigo'] . '.jpg'
                )){
                    $db->update('pu_candidatura', array('cand_status' => 1), 'cand_id=' . $value['cand_id']);
                    $ok++;
                }else{
                    $db->update('pu_candidatura', array('cand_status' => 3), 'cand_id=' . $value['cand_id']);
                    $erro++;
                }
            }
            Alex_Util::debugEscreve('COPIADAS............: ' . $ok);
            Alex_Util::debugEscreve('NÃO ENCONTRADAS.....: ' . $erro);
        }else{
            $this->finalizaLoop();
        }
    }
    
    public function extrairFotos2004($uf_id=1){
        $this->inicilizaLoop();
        if($this->view->contador > 0){
            return;
        }
        $db = Zend_Registry::get('db');
        $res = $db->fetchAll('
            SELECT C.cand_estado_id AS uf, C.cand_municipio_id AS municipio, D.cand_pessoa_cd_tse AS codigo
            FROM pu_candidatura C, pu_candidatura_detalhe D
            WHERE C.eleicao_id=2004
               && C.cand_id=D.cand_id
               && C.cand_estado_id=' . $uf_id . '
            LIMIT ' . $this->view->contador . ', 100'
        );
        if(count($res) > 0){
            $municipios = $tmp = array();
            foreach ($res as $value) {
                $tmp[$value['municipio']] = $value['municipio'];
            }
            
            $tmp = $db->fetchAll('SELECT municipio_id, municipio_cd_tse FROM pu_municipio WHERE municipio_id IN (' . implode(', ', $tmp) . ')');
            foreach ($tmp as $value) {
                $municipios[$value['municipio_id']] = str_pad($value['municipio_cd_tse'], 5, '0', STR_PAD_LEFT);
            }
            unset($tmp);
            
            $origem = 'http://www.tse.jus.br/sadEleicao2004DivCand/candidatoFoto.jsp?';
            $uf = Default_Model_Lista_Estado::get($uf_id);
            Alex_Util::debugEscreve($uf[0] . ' (' . $uf_id . ')....: ' . count($res));
            foreach ($res as $value) {
                echo "\n" . '<img src="' . $origem . 'sq_cand=' . $value['codigo'] . '&amp;sg_ue=' . $municipios[$value['municipio']] . '&amp;sg_ue_sup=' . $uf[0] .'" title="F' .  $uf[0] . '-' . $municipios[$value['municipio']] . '-' . $value['codigo'] . '.jpg">';
            }
            echo "\n";
        }else{
            $this->finalizaLoop();
        }
    }
    
    public function extrairFotos2000($uf_id=1){
        Alex_Util::debugEscreve('NÃO IMPLEMENTADO');
    }
    
    public function extrairFotos1996($uf_id=1){
        Alex_Util::debugEscreve('NÃO IMPLEMENTADO');
    }

    // FUNÇÕES //
    
    public function trataNome($nome){
        $nome = mb_strtolower($nome);
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
    
    public function limpaNome($nome) {
        $nome = mb_strtolower($nome);
        $nome = str_replace('ç', 'c', $nome);
        $nome = preg_replace('/(á)|(à)|(â)|(ä)|(ã)|(ª)/', 'a', $nome);
        $nome = preg_replace('/(é)|(è)|(ê)|(ë)/', 'e', $nome);
        $nome = preg_replace('/(í)|(ì)|(î)|(ï)/', 'i', $nome);
        $nome = preg_replace('/(ó)|(ò)|(ô)|(ö)|(õ)|(º)|(°)/', 'o', $nome);
        $nome = preg_replace('/(ú)|(ù)|(û)|(ü)/', 'u', $nome);
        return $nome;
    }
    
    protected function excluiPasta($dir){
        if ($dd = opendir($dir)) {
            while (false !== ($arq = readdir($dd))) {
                if($arq != '.' && $arq != '..'){
                    $path = "$dir/$arq";
                    if(is_dir($path)){
                        $this->excluiPasta($path);
                    }elseif(is_file($path)){
                        unlink($path);
                    }
                }
            }
            closedir($dd);
        }
        rmdir($dir);
    }
    
}