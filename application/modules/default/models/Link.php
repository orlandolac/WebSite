<?php
/* Copyright 2013 de Alex de Oliveira.
 * Este arquivo é parte do programa OPovoUnido.com. O OPovoUnido.com é um software livre; você pode
 * redistribuí-lo e/ou modificá-lo dentro dos termos da [GNU General Public License OU GNU Affero
 * General Public License] como publicada pela Fundação do Software Livre (FSF); na versão 3 da
 * Licença. Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * licença para maiores detalhes. Você deve ter recebido uma cópia da [GNU General Public License
 * OU GNU Affero General Public License], sob o título "LICENCA.txt", junto com este programa, se
 * não, acesse http://www.gnu.org/licenses/
 */
final class Default_Model_Link {

    public static function getData($url) {
        $data = array(
            'link' => '',
            'dominio' => '',
            'data' => '',
            'titulo' => '',
            'descricao' => '',
            'palavra-chave' => '',
            'imagens' => array()
        );
        
        // RECUPERA O HTML DO LINK //
        try {
            $tmp = curl_init();
            curl_setopt($tmp, CURLOPT_HEADER, 0);
            curl_setopt($tmp, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($tmp, CURLOPT_URL, $url);
            curl_setopt($tmp, CURLOPT_FOLLOWLOCATION, 1);
            $html = curl_exec($tmp);
            curl_close($tmp);
        } catch (Exception $exc) {
            return null;
        }
        
        // PREPARA LINK //
        $data['link'] = $url;
        $data['dominio'] = Alex_Util::getDominio($url);
        
        // CRIA DOCUMENTO COM O HTML RECUPERADO //
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        
        // RECUPERANDO META'S //
        $tags = $doc->getElementsByTagName('meta');
        foreach ($tags as $tag) {
            if($tag->getAttribute('name') == 'description'){
                $data['descricao'] = $tag->getAttribute('content');
            }elseif($tag->getAttribute('name') == 'keywords'){
                $data['palavra-chave'] = mb_strtolower($tag->getAttribute('content'));
            }else{
                if($tag->getAttribute('property')){
                    if($tag->getAttribute('property') == 'og:title'){
                        $data['titulo'] = $tag->getAttribute('content');
                    }elseif($tag->getAttribute('property') == 'og:image'){
//                        $tmp = @getimagesize($tag->getAttribute('content'));
//                        if($tmp[0] > 50){
//                            $data['imagens'][0] = array($tmp[0], $tmp[1], $tag->getAttribute('content'));
//                        }
                    }elseif($tag->getAttribute('property') == 'og:url'){
                        $data['link'] = $tag->getAttribute('content');
                    }elseif($tag->getAttribute('property') == 'article:published_time'){
                        $data['data'] = $tag->getAttribute('content');
                    }
                }
            }
        }
        
        // RECUPERA IMAGENS //
        //if(count($data['imagens']) < 1 || $data['imagens'][0][1] < 200){
            $tags = $doc->getElementsByTagName('img');
            foreach ($tags as $tag) {
                try {
                    $tmp = @getimagesize($tag->getAttribute('src'));
                } catch (Exception $exc) {
                    continue;
                }
                if($tmp[0] > 150){
                    $data['imagens'][] = array($tmp[0], $tmp[1], $tag->getAttribute('src'));
                }
            }
            sort($data['imagens']); // arsort
        //}
        
        // RECUPERANDO TITLE //
        if($data['titulo'] == ''){
            $tags = $doc->getElementsByTagName('title');
            if($tags->length > 0){
                $data['titulo'] = $tags->item(0)->nodeValue;
            }
        }
        
        return $data;
    }

}