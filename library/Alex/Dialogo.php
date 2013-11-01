<?php

class Alex_Dialogo {

    protected $mensagens = array();

    // METODOS //

    public function add($tipo, $texto) {
        $this->mensagens[] = new Alex_Dialogo_Mensagem($tipo, $texto);
    }

    public function cls() {
        $this->mensagens = array();
    }

    public function imprimir() {
        foreach ($this->mensagens as $mensagem) {
            $mensagem->imprimir();
        }
        $this->cls();
    }

    public function combine(Alex_Dialogo $dialogo) {
        foreach ($dialogo->lista() as $mensagem) {
            $this->add($mensagem);
        }
        return $this;
    }

    public function copy() {
        $temp = new Alex_Dialogo();
        foreach ($this->mensagens as $mensagem) {
            $data = $mensagem->getData();
            $temp->add(new Alex_Dialogo_Mensagem($data['tipo'], $data['texto']));
        }
        return $temp;
    }

    public function lista() {
        return $this->mensagens;
    }

    public function getMsn($id) {
        if(isset($this->mensagens[$id])){
            return new Alex_Dialogo_Mensagem($this->mensagens[$id]->getTipo(), $this->mensagens[$id]->getTexto());
        }else{
            return null;
        }
    }
    
}