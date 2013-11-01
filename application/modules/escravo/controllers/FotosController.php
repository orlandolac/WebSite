<?php
class Escravo_FotosController extends Zend_Controller_Action {
    
    #   MAJORITARIAS                # MUNICIPAIS
    #   2010    -> ok               # 2012    -> ?
    #   2006    -> ok               # 2008    -> ?
    #   2002    -> ok               # 2004    -> ?
    #   1998    -> não tem          # 2000    -> não tem
    #   1994    -> não tem          # 1996    -> não tem

    public function indexAction() {
        $tempo = time();
        $this->loopIni();
        
        $db = Zend_Registry::get('db');
        $p = $db->fetchRow('SELECT pessoa_id, pessoa_nome FROM pu_pessoa WHERE pessoa_status_id=0 && pessoa_flags=0 LIMIT 0, 1');
        Alex_Util::debugEscreve($p);
        if($p){
            $update = array('pessoa_status_id' => 1);
            $cands = $db->fetchAll('SELECT cand_id, cand_eleicao_id, cand_estado_id, cand_municipio_id FROM pu_candidatura WHERE cand_pessoa_id=' . $p['pessoa_id'] . ' ORDER BY cand_eleicao_id DESC');
            if(count($cands) > 0){
                foreach ($cands as $c) {
                    if($this->procuraImagem($p, $c)){
                        $update['pessoa_flags'] = 1;
                        break;
                    }
                }
            }
            $db->update('pu_pessoa', $update, 'pessoa_id=' . $p['pessoa_id']);
        }else{
            Alex_Util::debugEscreve('NINGUEM TEM STATUS ZERO');
            $this->loopFim();
        }
        Alex_Util::debugEscreve('QTD SEGUNDOS........: ' . (time()-$tempo));
    }
    
    ################################################################################################
    ################################################################################################
    ################################################################################################
    
    private function procuraImagem($pessoa, $candidatura) {
        switch ($candidatura['cand_eleicao_id']) {
            case 2012 : return $this->procuraImagem2012($pessoa, $candidatura);
            case 2008 : return $this->procuraImagem2008($pessoa, $candidatura);
            case 2004 : return $this->procuraImagem2004($pessoa, $candidatura);
        }
        return false;
    }
    
    private function procuraImagem2012($pessoa, $candidatura) {
        $db = Zend_Registry::get('db');
        $detalhe = $db->fetchRow('SELECT cand_pessoa_cd_tse FROM pu_candidatura_detalhe WHERE cand_id=' . $candidatura['cand_id']);
        
        $uf = Default_Model_Lista_Estado::get($candidatura['cand_estado_id']);
        
        return $this->setImagem(
            $pessoa,
            $candidatura,
            'http://el.imguol.com/2012/fichas/'.$uf[0].'/F'.$uf[0].$detalhe['cand_pessoa_cd_tse'].'.jpg'
        );
    }
    
    private function procuraImagem2008($pessoa, $candidatura) {
        $db = Zend_Registry::get('db');
        $detalhe = $db->fetchRow('SELECT cand_pessoa_cd_tse FROM pu_candidatura_detalhe WHERE cand_id=' . $candidatura['cand_id']);
        
        $municipio = Zend_Registry::get('db')->fetchRow('SELECT municipio_id, municipio_cd_tse FROM pu_municipio WHERE municipio_id=' . $candidatura['cand_municipio_id']);
        $municipio['municipio_cd_tse'] = str_pad($municipio['municipio_cd_tse'], 5, '0', STR_PAD_LEFT);
        
        return $this->setImagem(
            $pessoa,
            $candidatura,
            'http://www.tse.jus.br/sadEleicaoDivulgaCand2008/comuns/imagens/fotosCandidatosTemp/FotoCandidato-'.$municipio['municipio_cd_tse'].'-'.$detalhe['cand_pessoa_cd_tse'].'.jpg'
        );
    }
    
    private function procuraImagem2004($pessoa, $candidatura) {
        return false;
    }
    
    private function setImagem($pessoa, $candidatura, $link){
        $data = @getimagesize($link);
        if(isset($data['mime']) && substr($data['mime'], 0, 5) == 'image'){
            $data = array(
                'pessoa_id'   => $pessoa['pessoa_id'],
                'imagem_tipo' => $data['mime'],
            );
            
            try {
                $data['imagem_data'] = file_get_contents($link);
            } catch (Exception $exc) {
                try {
                    sleep(1);
                    $data['imagem_data'] = file_get_contents($link);
                } catch (Exception $exc) {
                    Alex_Util::debugEscreve($candidatura['cand_eleicao_id'] . ' NÃO CONSEGUIU A IMAGEM');
                    return false;
                }
            }

            try {
                Zend_Registry::get('db')->insert('pu_pessoa_imagem', $data);
            } catch (Exception $exc) {
                Alex_Util::debugEscreve($candidatura['cand_eleicao_id'] . ' IMAGEM NÃO INSERIDA');
                return false;
            }
            //Alex_Util::debugEscreve($candidatura['cand_eleicao_id'] . '<br/><img src="' . $link . '" /> &Gt; <img src="/dossies/ajax-foto/id/' . $pessoa['pessoa_id'] . '" />');
            Alex_Util::debugEscreve($candidatura['cand_eleicao_id'] . '................: OK</a>');
            return true;
        }else{
            //Alex_Util::debugEscreve($candidatura['cand_eleicao_id'] . ' NÃO ENCONTRADO.: <a href="' . $link . '" target="_blank">' . basename($link) . '</a>');
            Alex_Util::debugEscreve($candidatura['cand_eleicao_id'] . '................: ERRO</a>');
            return false;
        }
    }
    
    ################################################################################################
    ################################################################################################
    ################################################################################################
    ################################################################################################
    ################################################################################################
    
    // MÉTODOS //
    
    public function init() {
        if(Zend_Auth::getInstance()->hasIdentity()
        && Zend_Auth::getInstance()->getIdentity()->user_id == 1
        && Zend_Auth::getInstance()->getIdentity()->user_tipo_id == 3){
        }else{
            $this->_redirect('/');
        }
        $this->view->contador = 0;
    }
    
    public function loopIni(){
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
        if($sys->contador > 2000000){
            $this->finalizaLoop();
        }
    }
    
    public function loopFim(){
        $sys = new Zend_Session_Namespace('sys');
        $this->view->contador = $sys->contador = 0;
        Alex_Util::debugEscreve('LOOP FINALIZADO.....: OK');
    }
    
}