<?php

class Alex_Tabela extends Zend_Db_Table_Abstract {

    public $_db;
    protected $primary = array();
    protected $dialogo;
    //
    protected $cps = array();
    protected $cpsAux = array();
    protected $tmp;
    //
    protected $busca_select;
    protected $busca_pagina = 1;
    protected $busca_pagina_max_itens = 25;
    protected $busca_ordens = array();
    protected $busca_where = array();

    // CONSTRUTOR //

    public function __construct($config = array()) {
        parent::__construct($config);

        $sys = new Zend_Session_Namespace('sistem');
        $this->dialogo = $sys->dialogo;
    }

    // AÇÕES //

    public function buscar(array $post = null) {
        $this->busca_select = $this->_db->select();
        $this->busca_select->from(array('x' => $this->_name));

        $res['primary'] = $this->primary[0];
        $res['option'] = array(
            'busca_pagina' => $this->busca_pagina,
            'busca_pagina_limite' => 1,
            'busca_pagina_qtd_itens' => 0,
            'busca_pagina_max_itens' => $this->busca_pagina_max_itens,
            'busca_ordens' => $this->busca_ordens
        );

        $res['fields'] = array();
        if (isset($post)) {
            // Configura limite
            if (isset($post['busca_pagina_limite'])) {
                if ($post['busca_pagina_limite'] > 0) {
                    $res['option']['busca_pagina_limite'] = $post['busca_pagina_limite'];
                }
                unset($post['busca_pagina_limite']);
            }

            // Configura pagina atual
            if (isset($post['busca_pagina'])) {
                if ($post['busca_pagina'] > 0) {
                    $res['option']['busca_pagina'] = $post['busca_pagina'];
                } else {
                    $res['option']['busca_pagina'] = $res['option']['busca_pagina_limite'];
                }
                unset($post['busca_pagina']);
            }

            // Configura página
            if (isset($post['busca_pagina_max_itens'])) {
                if ($post['busca_pagina_max_itens'] > 0 && $post['busca_pagina_max_itens'] < 100) {
                    $res['option']['busca_pagina_max_itens'] = $post['busca_pagina_max_itens'];
                }
                unset($post['busca_pagina_max_itens']);
            }

            // Filtra campos
            foreach ($post as $cp => $valor) {
                if (isset($this->cps[$cp]) || isset($this->cpsAux[$cp])) {
                    $res['fields'][$cp] = $valor;
                }
            }
        }

        // Prepara os filtros de pesquisa
        $where = $this->busca_where;
        foreach ($res['fields'] as $campo => $valor) {
            if ($valor != '') {
                if (is_numeric($valor)) {
                    $tmp = explode('_', $campo);
                    if ($tmp[(count($tmp) - 1)] == 'id') {
                        $where[] = $this->getDefaultAdapter()->quoteInto('x.' . $campo . ' = ?', $valor);
                        continue;
                    }
                }
                $where[] = $this->getDefaultAdapter()->quoteInto($campo . ' like ?', '%' . $valor . '%');
            }
        }

        // Monta o where
        if (count($where) > 0) {
            $this->busca_select->where(implode(' AND ', $where));
        }

        // Configurar limite de paginas
        if ($res['option']['busca_pagina'] == 2 && $res['option']['busca_pagina_limite'] == 1) {
            $tmp = $this->busca_select->query()->fetchAll();
            if (count($tmp) > 0) {
                $res['option']['busca_pagina_limite'] = ceil(count($tmp) / $res['option']['busca_pagina_max_itens']);
            }
//            $tmp = $this->_db->fetchRow('SELECT count(*) qtd' . substr($SQL, 8));
//            if ($tmp['qtd'] > 0) {
//                $res['option']['pagina_limite'] = ceil($tmp['qtd'] / $res['option']['pagina_max_itens']);
//            }
        }

        // Configura a ordem do resultado da consulta na tabela
        $this->busca_select->order($res['option']['busca_ordens']);

        // Configura o limite da consulta.
        $this->busca_select->limit($res['option']['busca_pagina_max_itens'], ($res['option']['busca_pagina'] - 1) * $res['option']['busca_pagina_max_itens']);

        // Executa a consulta na tabela.
        $tmp = $this->busca_select->query()->fetchAll();
        $res['option']['busca_pagina_qtd_itens'] = count($tmp);
        if ($res['option']['busca_pagina_qtd_itens'] > 0) {
            if ($res['option']['busca_pagina'] > $res['option']['busca_pagina_limite']) {
                $res['option']['busca_pagina_limite'] = $res['option']['busca_pagina'];
            }
        }
        $res['paginador'] = $tmp;
        return $res;
    }

    public function detalhar($where) {
        $this->preDetalhar();
        $data = $this->fetchRow($where);
        if ($data) {
            $this->setCps($data->toArray());
            $this->posDetalhar();
            return true;
        }
        return null;
    }

    public function salvar() {
        if ($this->eSalvo()) {
            if ($this->eValido(false)) {
                $where = $this->preAlterar();
                if ($where) {
                    if (parent::update($this->cps, $where)) {
                        $this->dialogo->add('Ok', 'Alteração realizada com sucesso!');
                        $this->posAlterar($where);
                        return true;
                    }
                    if ($this->posAlterarNot($where)) {
                        return true;
                    } else {
                        $this->dialogo->add('Alerta', 'Nenhuma alteração foi realizada.');
                    }
                }
            } else {
                $this->dialogo->add('Alerta', 'Nenhuma alteração foi realizada.');
            }
        } else {
            if ($this->eValido()) {
                $this->preCadastrar();
                $res = parent::insert($this->cps);
                if(is_array($res)){
                    foreach ($res as $key => $value) {
                        $this->cps[$key] = $value;
                    }
                    $this->dialogo->add('Ok', 'Cadastro realizado com sucesso!');
                    $this->posCadastrar();
                    return true;
                }else{
                    if ($res > 0) {
                        $this->cps[$this->primary[0]] = $res;
                        $this->dialogo->add('Ok', 'Cadastro realizado com sucesso!');
                        $this->posCadastrar();
                        return true;
                    } else {
                        $this->dialogo->add('Erro', 'Aconteceu um erro inesperado e o cadastro não pode ser realizado.');
                    }
                }
            } else {
                $this->dialogo->add('Alerta', 'O cadastro não foi realizado.');
            }
        }
        return false;
    }

    public function excluir() {
        if ($this->eSalvo()) {
            $where = $this->eExcluivel();
            if ($where) {
                $this->preDeletar($where);
                if (parent::delete(implode(' AND ', $where))) {
                    $this->posDeletar($where);
                    $this->dialogo->add('Ok', 'O registro foi excluído com sucesso!');
                    return true;
                } else {
                    $this->posDeletarNot($where);
                }
                $this->dialogo->add('Erro', 'Algum erro inesperado aconteceu e o registro não pode ser excluído.');
            } else {
                $this->dialogo->add('Alerta', 'O registro não pode ser excluído.');
            }
        } else {
            $this->dialogo->add('Alerta', 'O registro ainda não foi registrado');
        }
        return false;
    }

    // MÉTODOS //

    public function getPrimary() {
        return $this->primary;
    }
    
    public function getPrimaryValue() {
        if(count($this->primary) > 1){
            $string = array();
            foreach ($this->primary as $key => $value) {
                $string[count($string)] = $this->getCps($value);
            }
            $string = implode('-', $string);
        }else{
            $string = $this->getCps($this->primary[0]);
        }
        return $string;
    }
    
    public function setCps(array $data) {
        foreach ($data as $cp => $valor) {
            if (isset($this->cps[$cp])) {
                $this->cps[$cp] = $valor;
            }
            if (isset($this->cpsAux[$cp])) {
                $this->cpsAux[$cp] = $valor;
            }
        }
    }

    public function getCps($cp = null) {
        if (isset($cp)) {
            if (isset($this->cps[$cp])) {
                return $this->cps[$cp];
            } elseif (isset($this->cpsAux[$cp])) {
                return $this->cpsAux[$cp];
            } else {
                return null;
            }
        } else {
            return array_merge($this->cps, $this->cpsAux);
        }
    }

    public function eSalvo() {
        if (isset($this->cps[$this->primary[0]]) && $this->cps[$this->primary[0]] > 0) {
            return true;
        }
        $this->cps[$this->primary[0]] = null;
        $this->cpsAux[$this->primary[0]] = null;
        return false;
    }

    public function eValido($novo = true) {
        $this->dialogo->add('Erro', 'A rotina de validação de dados não foi sobrescrita.');
        return false;
    }

    public function eExcluivel() {
        $this->dialogo->add('Erro', 'A rotina de validação de exclusão não foi sobrescrita.');
        return false;
    }

    // GATILHOS //

    protected function preDetalhar() {

    }

    protected function posDetalhar() {

    }

    protected function preCadastrar() {

    }

    protected function posCadastrar() {

    }

    protected function preAlterar() {
        $this->dialogo->add('Erro', 'A rotina que especifica o registra aser alterado não foi sobrescrita.');
        return false;
    }

    protected function posAlterar($where) {

    }

    protected function posAlterarNot($where, $direto = true) {
        return false;
    }

    protected function preDeletar($where) {

    }

    protected function posDeletar($where) {

    }

    protected function posDeletarNot($where) {

    }

}