<?php
/**
 * Disponibiliza serviços de acesso aos dados das proposições que tramitaram ou que estão em
 * tramitação na Câmara dos Deputados.
 */
final class Camara_SessoesReunioes extends Camara_WebService {

    protected static $controller = 'Proposicoes.asmx';

    /**
     * Método para consultas personalizadas relacionadas as sessões.
     * 
     * @param  string $view
     * @param  string $post
     * @return SimpleXMLObject
     */
    public static function obterData($view, array $post = array()) {
        return parent::obterService(self::$controller, $view, $post);
    }
    
    /**
     * Retorna a lista dos deputados que proferiam discurso no Plenário da Cãmara dos Deputados em um determinado período.
     * 
     * @param date      DataIni                 Data início do período desejado (formato: DD/MM/AAAA)
     * @param date      DataFim                 Data início do período desejado (formato: DD/MM/AAAA)
     * @param string    CodigoSessao            Código da sessão a ser pesquisada
     * @param string    ParteNomeParlamentar    Parte do nome do Deputado a ser pesquisada
     * @param string    SiglaPartido            Sigla do Partido do Deputado
     * @param string    SiglaUF                 Sigla da UF do Deputado
     * 
     * @return SimpleXMLObject Lista das sessões plenárias contendo os discursos proferidos.
     * <pre>
     * List<Sessao>{
     * Codigo           string                  Código da sessão plenária
     * Data             Date                    Data em que ocorreu a sessão
     * Numero           Int                     Número da sessão
     * Tipo             String                  Tipo da sessão
     * FasesSessao	LIST <FaseSessao>	Lista contendo as fases da sessão com os discursos proferidos
     * }
     * </pre>
     */
    public static function listarDiscursosPlenario(array $post) {
        return self::obterData('ListarDiscursosPlenario', $post);
    }
    
    /**
     * Retorna a lista de presença de deputados em um determinado dia.
     * 
     * @param data      $data                       Data da Sessão
     * @param int       $numMatriculaParlamentar    Numero da matrícula do Parlamentar obtido pelo método ObterDeputados
     * @param string    $siglaPartido               Sigla do Partido
     * @param string    $siglaUF                    Sigla da UF a ser pesquisada
     * 
     * @return SimpleXMLObject Agrupado por Sessao que ocorreram num mesmo período
     * <pre>
     * List<diaDeSessao>{
     * dia              Date                    Retona a data pesquisada(dd/mm/yyyy)
     * qtdeSessoesDia	Int                     Quantidade de sessões que ocorreram no dia
     * frequenciasDia	LIST<FrequenciaDia>	Lista as frequências dos Parlamentares ocorridas num determinado período
     * }
     * </pre>
     */
    public static function listarPresencasDia(array $post) {
        return self::obterData('ListarPresencasDia', $post);
    }
    
    /**
     * Retorna as presenças de um deputado em um determinado período.
     * 
     * @param date      $dataIni                       Data inicial
     * @param daye      $dataFim                       Data Final
     * @param int       $numMatriculaParlamentar       Numero da matrícula do Parlamentar obtido pelo método ObterDeputados
     * 
     * @return SimpleXMLObject  Agrupado por Sessao que ocorreram num mesmo período
     * <pre>
     * List<Proposicao>{
     * parlamentar	List	Retorna Informações do Parlamentar pesquisado
     * diaDeSessao	List	Organizado por dia e quantidades de sessões no dia
     * }
     * </pre>
     */
    public static function listarPresencasParlamentar(array $post) {
        return self::obterData('ListarPresencasParlamentar', $post);
    }
    
    /**
     * Retorna a lista de situações para as reuniões de comissão e sessões plenárias da Câmara dos Deputados
     * 
     * @return SimpleXMLObject Lista dos tipos de situação das reuniões e sessões plenárias
     * <pre>
     * List<SituacaoReuniaoSessao>{
     * @ID          int         ID da situação da reunão ou sessão plenária
     * @Descricao   String	Descrição da situação da reunão ou sessão plenária
     * }
     * </pre>
     */
    public static function listarSituacoesReuniaoSessao() {
        return self::obterData('ListarSituacoesReuniaoSessao');
    }
    
    /**
     * Retorna o inteiro teor do discurso proferido no Plenário.
     * 
     * @param
     * 
     * @return SimpleXMLObject 
     * <pre>
     * List<Proposicao>{
     * }
     * </pre>
     */
    public static function obterInteiroTeorDiscursosPlenario() {
        return self::obterData('ObterInteiroTeorDiscursosPlenario');
    }
    
}