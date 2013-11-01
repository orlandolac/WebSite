<?php

class Alex_Util {

    // METODOS //

    public static function getDominio($link) {
        $data = parse_url($link);
        if(substr($data['host'], 0, 4) == 'www.'){
            return substr($data['host'], 4);
        }else{
            return $data['host'];
        }
    }
    
    public static function getIdade($data) {
        echo '~' . ceil(((time()-strtotime($data))/31536000));
    }

    public static function keyToString(array $data, array $primary) {
        if (count($primary) == 1) {
            return $data[$primary[0]];
        } else {
            $string_key = array();
            foreach ($primary as $campo) {
                $string_key[] = $data[$campo];
            }
            return implode('-', $string_key);
        }
    }

    public static function isPost(array $post, $campo, $default = '') {
        if (isset($post[$campo])) {
            echo ($post[$campo] == '') ? ($default) : ($post[$campo]);
        } else {
            echo $default;
        }
    }

    public static function whitePost(array $post, $campo, $default = '') {
        if (isset($post[$campo])) {
            echo ($post[$campo] == '') ? ($default) : ($post[$campo]);
        } else {
            echo $default;
        }
    }

    public static function newDica($data) {
        return implode('                                                                                                     ', $data);
    }

    public static function newIcone($class = '', $href = '', $onclick = '', $src = '', $title = '') {
        if($href == '' || $href == '#'){
            echo '<div';
            echo ' class="' . $class . '"';
            echo ' onclick="' . $onclick . '"';
            echo ' title="' . $title . '"';
            echo ' alt="' . $title . '"';
            echo '>';
            echo '<img';
            echo ' src="' . $src . '"';
            echo ' title="' . $title . '"';
            echo ' alt="' . $title . '"';
            echo '/>';
            echo '</div>';
        }else{
            echo '<a';
            echo ' href="' . $href . '"';
            echo ' class="' . $class . '"';
            echo ' onclick="' . $onclick . '"';
            echo ' title="' . $title . '"';
            echo ' alt="' . $title . '"';
            echo '>';
            echo '<img';
            echo ' src="' . $src . '"';
            echo ' title="' . $title . '"';
            echo ' alt="' . $title . '"';
            echo '/>';
            echo '</a>';
        }

    }

    public static function debugExibi($debug = array()) {
        echo '
            <style  type="text/css">
                    .debugAlex {
                            border: none;
                            background-color: #ee9;
                            padding: 10px;
                            width: 28px;
                            height: 28px;
                            min-width: 28px;
                            min-height: 28px;
                            position: fixed;
                            top:37px;
                            left:8px;
                            z-index: 999999;
                    }

                    .debugAlex:HOVER {
                            width: 800px;
                            height: 500px;
                    }
            </style>
            ';

        echo '<textarea class="debugAlex">';
        echo "Debug\n-----------------------------------------------------------------------------\n";
        print_r($debug);
        echo '</textarea>';
    }

    public static function debugEscreve($debug = array()) {
        echo '<pre style="background: #fff; text-align:left; border: double 4px #000; padding:10px; font-size:25px;">';
        if (is_array($debug)) {
            if (count($debug) > 0) {
                print_r($debug);
            } else {
                echo '<p>|--->AKI<---|</p>';
            }
        } else {
            print_r($debug);
        }
        echo '</pre>';
    }

    public static function data($times) {
        if($times >= 1){
            $d = explode('-', date('d-m-Y', $times));
            return $d[0]. ' de ' . Alex_Util::mes($d[1]). ' de ' . $d[2] . ', ás ' . date('H:i:s', $times) . '.';
        }else{
            return '';
        }
    }

    public static function mes($mes = 0) {
        $meses = array(
             1 => 'Janeiro',
             2 => 'Fevereiro',
             3 => 'Março',
             4 => 'Abril',
             5 => 'Maio',
             6 => 'Junho',
             7 => 'Julho',
             8 => 'Agosto',
             9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        );
        $mes = $mes+0;
        if($mes > 0){
            return $meses[$mes];
        }else{
            return $meses;
        }
    }

    public static function descreveTempo($segundos, $op=null){
        $s = $i = $h = $d = $n = $m = $a = 0;
        $segundos = (int)$segundos;
        $tempo = array();

        if(!is_null($op)){
            $segundos = time() - $segundos;
        }

        $s = (int)($segundos%60);
        $i = (int)($segundos/60);
        if($i >= 60){
            $h = (int)($i/60);
            $i = (int)($i%60);
            if($h >= 24){
                $d = (int)($h/24);
                $h = (int)($h%24);
                if($d >= 30){
                    $m = (int)($d/30);
                    $d = (int)($d%30);
                    if($m >= 12){
                        $a = (int)($m/12);
                        $m = (int)($m%12);
                    }
                }
                if($d >= 7){
                    $n = (int)($d/7);
                    $d = (int)($d%7);
                }
            }
        }
        if($a > 0){
            $tempo[] = $a . (($a == 1)?(' ano'):(' anos'));
        }
        if($m > 0){
            $tempo[] = $m . (($m == 1)?(' mês'):(' meses'));
        }
        if(count($tempo) < 2 && $n > 0){
            $tempo[] = $n . (($n == 1)?(' semana'):(' semanas'));
        }
        if(count($tempo) < 2 && $d > 0){
            $tempo[] = $d . (($d == 1)?(' dia'):(' dias'));
        }
        if(count($tempo) < 2 && $h > 0){
            $tempo[] = $h . (($h == 1)?(' hora'):(' horas'));
        }
        if(count($tempo) < 2 && $i > 0){
            $tempo[] = $i . (($i == 1)?(' minuto'):(' minutos'));
        }
        if(count($tempo) < 2 && $s > 0){
            $tempo[] = $s . (($s == 1)?(' segundo'):(' segundos'));
        }
        return implode(' e ', $tempo);
    }

    public static function trataNome($string, $op=null){
        $string = mb_strtolower($string);
        $string = str_replace('ç', 'c', $string);
        $string = preg_replace('/(á)|(à)|(â)|(ä)|(ã)|(ª)|(@)/', 'a', $string);
        $string = preg_replace('/(é)|(è)|(ê)|(ë)|(&)/', 'e', $string);
        $string = preg_replace('/(í)|(ì)|(î)|(ï)/', 'i', $string);
        $string = preg_replace('/(ó)|(ò)|(ô)|(ö)|(õ)|(º)/', 'o', $string);
        $string = preg_replace('/(ú)|(ù)|(û)|(ü)/', 'u', $string);
        $string = preg_replace('/\s+|\s+/', ' ', $string);
        $string = preg_replace('/[^a-z0-9\s$]/', '', $string);

        if(!is_null($op)){
            $string = preg_replace('/[^a-z0-9$]/', $op, $string);
            if(strlen($op) > 0){
                $fil = "/$op+|$op+/";
                $string = preg_replace($fil, $op, $string);
            }
        }

        return $string;
    }

}