<?php
/**
 * Disponibiliza serviços de acesso aos dados dos órgãos legislativos da Câmara dos Deputados.
 */
final class Camara_Orgaos extends Camara_WebService {

    protected static $controller = 'Orgaos.asmx';
    
    /**
     * Método para consultas personalizadas relacionadas aos órgãos.
     * 
     * @param  string $view
     * @param  string $post
     * 
     * @return SimpleXMLObject
     */
    public static function obterData($view, $post = array()) {
        return parent::obterService(self::$controller, $view, $post);
    }
    
    /**
     * Retorna a lista dos tipos de cargo para os órgãos legislativos da Câmara dos Deputados
     * (ex: presidente, primeiro-secretário, etc).
     *
     * @return SimpleXMLObject
     */
    public static function listarCargosOrgaosLegislativosCD() {
        return self::obterData('ListarCargosOrgaosLegislativosCD');
    }
    
    /**
     * Retorna a lista dos tipos de órgãos que participam do processo legislativo na Câmara dos
     * Deputados.
     *
     * @return SimpleXMLObject
     */
    public static function listarTiposOrgaos() {
        return self::obterData('ListarTiposOrgaos');
    }
    
    /**
     * Retorna o andamento de uma proposição pelos órgãos internos da Câmara a partir de uma data
     * específica.
     * 
     * @param  int $sigla
     * @param  int $numero
     * @param  int $ano
     * @param  int $datIni
     * @param  int $idOrgao
     * 
     * @return SimpleXMLObject
     */
    public static function obterAndamento($post = array()) {
        return self::obterData('ObterAndamento', $post);
    }
    
    /**
     * Retorna as emendas, substitutivos e redações finais de uma determinada proposição.
     *
     * @param  int $tipo
     * @param  int $numero
     * @param  int $ano
     * @return SimpleXMLObject
     */
    public static function obterEmendasSubstitutivoRedacaoFinal($post = array()) {
        return self::obterData('ObterEmendasSubstitutivoRedacaoFinal', $post);
    }
            
    /**
     * Retorna os dados de relatores e pareces, e o link para a íntegra de uma determinada proposição.
     *
     * @param  int $tipo
     * @param  int $numero
     * @param  int $ano
     * @return SimpleXMLObject
     */
    public static function obterIntegraComissoesRelator($post = array()) {
        return self::obterData('ObterIntegraComissoesRelator', $post);
    }
    
    /**
     * Retorna os parlamentares membros de uma determinada comissão.
     *
     * @param  int $idOrgao
     * @return SimpleXMLObject
     */
    public static function obterMembrosOrgao($post = array()) {
        return self::obterData('ObterMembrosOrgao', $post);
    }
    
    /**
     * Retorna a lista de órgãos legislativos da Câmara dos Deputados (comissões, Mesa Diretora,
     * conselhos, etc).
     * 
     * @return SimpleXMLObject
     */
    public static function obterOrgaos() {
        return self::obterData('ObterOrgaos');
    }
    
    /**
     * Retorna as pautas das reuniões de comissões e das sessões plenárias realizadas em um
     * determinado período.
     * 
     * @param  int $idOrgao
     * @param  int $datIni
     * @param  int $datFim
     * @return SimpleXMLObject
     */
    public static function obterPauta($post = array()) {
        return self::obterData('obterPauta', $post);
    }
    
    /**
     * Retorna as pautas das reuniões de comissões e das sessões plenárias realizadas em um
     * determinado período.
     * 
     * @param  int $datIni
     * @param  int $datFim
     * @return SimpleXMLObject
     */
    public static function obterPautaPeriodo($post = array()) {
        return self::obterData('ObterPautaPeriodo', $post);
    }
    
    /**
     * Retorna os dados do último despacho da proposição.
     *
     * @param  int $tipo
     * @param  int $numero
     * @param  int $ano
     * @return SimpleXMLObject
     */
    public static function obterRegimeTramitacaoDespacho($post = array()) {
        return self::obterData('ObterRegimeTramitacaoDespacho', $post);
    }

}