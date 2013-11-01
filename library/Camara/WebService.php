<?php

class Camara_WebService {

    /**
     * Endereço do Web Service.
     */
    protected static $service = 'http://www.camara.gov.br/SitCamaraWS';
    
    /**
     * Método para consultas personalizadas relacionadas aos dados da câmara.
     * 
     * @param  string $controller
     * @param  string $view
     * @param  string $query
     * 
     * @return SimpleXMLObject
     */
    public static function obterService($controller, $view, array $post = array()) {
        $client = new Zend_Http_Client(self::$service . '/' . $controller . '/' . $view, array('timeout' => 60));
        $data = $client->setParameterPost($post)
                ->request(Zend_Http_Client::POST)
                ->getBody();
        if(substr($data, 0, 1) == '<'){
            $data = (array) simplexml_load_string($data);
        }else{
            return null;
        }
        
        foreach ($data as $key => $value) {
            if(is_object($value)){
                if(get_class($value) == 'SimpleXMLElement'){
                    $data[$key] = array((array) $value);
                }
            }
            break;
        }
        return self::simpleXmlToArray($data);
    }
    
    /**
     * Converte todos os objetos SimpleXMLElement para array em uma lista. 
     * 
     * @param  array $lista A Lista que deve ser varida e convertida.
     * 
     * @return array
     */
    public static function simpleXmlToArray($lista) {
        foreach ($lista as $key => $value) {
            if(is_object($value)){
                if(get_class($value) == 'SimpleXMLElement'){
                    $value = (array) $value;
                }
            }
            if(is_array($value)){
                $value = self::simpleXmlToArray($value);
            }
            $lista[$key] = $value;
        }
        return $lista;
    }
    
}