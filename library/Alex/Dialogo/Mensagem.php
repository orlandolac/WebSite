<?php

class Alex_Dialogo_Mensagem {

    public static $id = 0;
    protected $tipo = '';
    protected $texto = '';
    
    // CONSTRUTOR //
    
    public function __construct($tipo, $texto) {
        $this->tipo = $tipo;
        $this->texto = $texto;
    }
    
    // MÉTODOS //

    public function imprimir() {
        $id = self::getNewId();
        echo '<div class="msn ' . $this->tipo . '" id="msn' . $id . '">';
        echo '<div class="texto">';
        if(is_array($this->texto)){
            echo '<pre>';
            print_r($this->texto);
            echo '</pre>';
        }else{
            echo $this->texto;
        }
        echo '</div>';
        echo '<div class="fechar" onclick="document.getElementById(\'msn' . $id . '\').style.display = \'none\'">x</div>';
        echo '</div>';
    }

    public function getData() {
        return array('tipo' => $this->tipo, 'texto' => $this->texto);
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getTexto() {
        return $this->texto;
    }

    public static function getNewId(){
        self::$id++;
        return self::$id;
    }
    
}