<?php

class Extracao_JoinController extends Zend_Controller_Action {

    public function indexAction() {
        $this->_redirect('/extracao/join/titulo');
    }
    
    public function tituloAction() {
        
        // TITULO DE ELEITOR //
        $db = Zend_Registry::get('db');
        $res = $db->fetchRow('
            SELECT *
            FROM lista_duplicados_titulos
            WHERE qtd > 1
            ORDER BY qtd DESC
            LIMIT 0, 1
        ');
//        $res = $db->fetchRow('
//            SELECT pessoa_titulo_eleitor, count(*) AS qtd
//            FROM pu_pessoa
//            WHERE pessoa_titulo_eleitor > 0
//            GROUP BY pessoa_titulo_eleitor
//            ORDER BY qtd DESC
//            LIMIT 0, 1
//        ');
        if($res){
            $this->_redirect('/extracao/join/comparacao/titulo/' . $res['pessoa_titulo_eleitor']);
        }else{
            $this->_redirect('/extracao/join/comparacao');
        }
    }
    
    public function cpfAction() {
        $db = Zend_Registry::get('db');
        // CPF //
        $res = $db->fetchRow('
            SELECT pessoa_cpf, count(*) AS qtd
            FROM pu_pessoa
            WHERE pessoa_cpf > 0
            GROUP BY pessoa_cpf
            ORDER BY qtd DESC
            LIMIT 0, 1
        ');
        if($res){
            $this->_redirect('/extracao/join/comparacao/cpf/' . $res['pessoa_titulo_eleitor']);
        }else{
            $this->_redirect('/extracao/join/comparacao');
        }
    }
    
    public function comparacaoAction() {
        $sys = new Zend_Session_Namespace('sys');
        if(!isset($sys->join)){
            $sys->join=0;
        }
        $this->view->join = $sys->join;
        
        $db = Zend_Registry::get('db');
        $get = $this->getRequest()->getParams();
        
        if($this->getRequest()->isPost()){
            if(isset($get['p_op'])){
                if($get['p_op'] == 'UPDATE'){
                    $this->update($get);
                }elseif($get['p_op'] == 'DELETE'){
                    $this->delete($get);
                }elseif($get['p_op'] == 'JOIN'){
                    $this->join($get);
                }else{
                    Alex_Util::debugEscreve('OPERAÇÃO INVÁLIDA');
                }
            }else{
                Alex_Util::debugEscreve('SEM OPERAÇÃO');
            }
        }
        
        if(isset($get['titulo']) && $get['titulo'] > 0){
            $this->view->lista = $db->fetchAll('
                SELECT *
                FROM pu_pessoa P, pu_pessoa_detalhe D
                WHERE P.pessoa_titulo_eleitor=' . $db->quoteInto('?', $get['titulo']) .'
                   && P.pessoa_id=D.pessoa_id
                ORDER BY P.pessoa_id
            ');
        }elseif(isset($get['cpf']) && $get['cpf'] > 0){
            $this->view->lista = $db->fetchAll('
                SELECT *
                FROM pu_pessoa P, pu_pessoa_detalhe D
                WHERE P.pessoa_cpf=' . $db->quoteInto('?', $get['cpf']) .'
                   && P.pessoa_id=D.pessoa_id
                ORDER BY P.pessoa_id
            ');
        }else{
            $this->view->lista = array();
            Alex_Util::debugEscreve('Nenhum TÍTULO ou CPF foi indicado.');
        }
        $this->view->get = $get;
    }

    // FUNÇÕES //
    
    protected function join($get) {
        $sys = new Zend_Session_Namespace('sys');
        if(isset($sys->join)){
            $sys->join++;
        }else{
            $sys->join=1;
        }
        
        $db = Zend_Registry::get('db');
        $pessoas = array();
        
        if(isset($get['titulo']) && $get['titulo'] > 0){
            $pessoas = $db->fetchAll('SELECT * FROM pu_pessoa WHERE pessoa_titulo_eleitor=' . $db->quoteInto('?', $get['titulo']) .' ORDER BY pessoa_id');
        }elseif(isset($get['cpf']) && $get['cpf'] > 0){
            $pessoas = $db->fetchAll('SELECT * FROM pu_pessoa WHERE pessoa_cpf=' . $db->quoteInto('?', $get['cpf']) .' ORDER BY pessoa_id');
        }else{
            Alex_Util::debugEscreve('REQUISIÇÃO INVÁLIDA');
        }
        
        if($pessoas > 1){
            // DESTACA PRINCIPAL //
            $principal = $pessoas[0];
            $principal_dir = Default_Model_Diretorio::getDiretorioPessoa($principal['pessoa_id']);
            unset($pessoas[0]);
            
            // SALVA FOTO //
            if(!file_exists(PUBLIC_PATH . '/uploads/pessoas' . $principal_dir . '/foto.jpg')){
                foreach ($pessoas as $value) {
                    $dir = Default_Model_Diretorio::getDiretorioPessoa($value['pessoa_id']);
                    if(file_exists(PUBLIC_PATH . '/uploads/pessoas' . $dir . '/foto.jpg')){
                        copy(PUBLIC_PATH . '/uploads/pessoas' . $dir . '/foto.jpg',
                             PUBLIC_PATH . '/uploads/pessoas' . $principal_dir . '/foto.jpg'
                        );
                        $db->update('pu_pessoa', array('pessoa_flags' => '10000'), 'pessoa_id=' . $principal['pessoa_id']);
                        Alex_Util::debugEscreve('FOTO ASSOCIADA');
                        break;
                    }
                }
            }
            // CONCATENAÇÃO E REMOÇÃO //
            foreach ($pessoas as $value) {
                // DELETE //
                //$db->delete('pu_pessoa_termo_indice'                , 'pessoa_id=' . $value['pessoa_id']);
                $db->delete('pu_pessoa_detalhe'                     , 'pessoa_id=' . $value['pessoa_id']);
                $db->delete('pu_pessoa'                             , 'pessoa_id=' . $value['pessoa_id']);
                
                // UPDATE //
                //$db->update('pu_partido_diretorio_x_cargo_x_pessoa' , array('pessoa_id' => $principal['pessoa_id']), 'pessoa_id=' . $value['pessoa_id']);
                $db->update('pu_candidatura'                        , array('pessoa_id' => $principal['pessoa_id']), 'pessoa_id=' . $value['pessoa_id']);
                $db->update('pu_bem'                                , array('pessoa_id' => $principal['pessoa_id']), 'pessoa_id=' . $value['pessoa_id']);
                
                // DIRETÓRIO //
                $this->excluiPasta(PUBLIC_PATH . '/uploads/pessoas' . Default_Model_Diretorio::getDiretorioPessoa($value['pessoa_id']));
            }
            $db->delete('lista_duplicados_titulos', 'pessoa_titulo_eleitor=' . $value['pessoa_titulo_eleitor']);
            $this->_redirect('/extracao/join');
        }else{
            Alex_Util::debugEscreve('NENHUMA ALTERAÇÃO FOI PROCESSADA');
        }
    }
    
    protected function delete($get) {
        if(isset($get['p_id']) && $get['p_id'] > 0){
            $db = Zend_Registry::get('db');
            $tmp = $db->fetchAll('SELECT cand_id FROM pu_candidatura WHERE pessoa_id=' . $get['p_id']);
            foreach ($tmp as $value) {
                $db->delete('pro_projeto_x_candidato', 'cand_id=' . $value['cand_id']);
                $db->delete('pu_candidatura_detalhe', 'cand_id=' . $value['cand_id']);
                $db->delete('pu_candidatura', 'cand_id=' . $value['cand_id']);
            }
            $db->delete('pu_partido_diretorio_x_cargo_x_pessoa', 'pessoa_id=' . $get['p_id']);
            $db->delete('pu_pessoa_termo_indice', 'pessoa_id=' . $get['p_id']);
            $db->delete('pu_bem', 'pessoa_id=' . $get['p_id']);
            $db->delete('pu_pessoa_detalhe', 'pessoa_id=' . $get['p_id']);
            $db->delete('pu_pessoa', 'pessoa_id=' . $get['p_id']);
            Alex_Util::debugEscreve('PESSOAS EXCLUIDA: ' . $get['p_id']);
        }else{
            Alex_Util::debugEscreve('REQUISIÇÃO INVÁLIDA');
        }
    }
        
    protected function update($get) {
        if(isset($get['p_id']) && $get['p_id'] > 0){
            $data = array(
                'pessoa_titulo_eleitor' => $get['p_te'],
                'pessoa_cpf'            => $get['p_pf'],
                'pessoa_nome'           => $get['p_nm'],
                'pessoa_apelido'        => $get['p_ap']
            );
            
            $db = Zend_Registry::get('db');
            $db->update('pu_pessoa', $data, 'pessoa_id=' . $get['p_id']);
            Alex_Util::debugEscreve('ALTERAÇÃO REALIZADA COM SUCESSO!');
        }else{
            Alex_Util::debugEscreve('REQUISIÇÃO INVÁLIDA');
        }
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