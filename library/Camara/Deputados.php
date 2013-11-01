<?php

/**
 * Disponibiliza serviços de acesso aos dados de deputados federais.
 */
final class Camara_Deputados extends Camara_WebService {

    protected static $controller = 'Deputados.asmx';
    
    /**
     * Método para consultas personalizadas relacionadas aos deputados.
     * 
     * @param  string $view
     * @param  string $post
     * @return SimpleXMLObject
     */
    public static function obterData($view, $post = array()) {
        return parent::obterService(self::$controller, $view, $post);
    }

    /**
     * Retorna os deputados em exercício na Câmara dos Deputados.
     * 
     * @return SimpleXMLObject Lista dos deputados em exercício
     * <pre>
     * List<Deputado>{
     *  ideCadastro	Int         ID do parlamentar
     *  condicao        String      Retorna se o deputado e Titular ou suplente
     *  Nome            String      Nome civil do parlamentar
     *  NomeParlamentar String      Nome de tratamento do parlamentar
     *  Sexo            String      Sexo (masculino ou feminino)
     *  UF              String      Unidade da Federação de representação do parlamentar
     *  Partido         String      Sigla do partido que o parlamentar representa
     *  Gabinete        String      Numero do Gabinete do parlamentar
     *  Anexo           String      Anexo (prédio) onde o gabinete está localizado
     *  Fone            String      Numero do telefone do gabinete
     *  Email           String      Email institucional do parlamentar
     *  Comissoes	comissoes   Comissões da Câmara dos Deputados que o parlamentar é membro
     * }
     * <pre>
     */
    public static function obterDeputados() {
        return self::obterData('ObterDeputados');
    }

    /**
     * Retorna os deputados líderes e vice-líderes em exercício das bancadas dos partidos.
     * 
     * @return SimpleXMLObject Lista de bancadas partidárias com os seus respectívos líderes
     * <pre>
     * List<Deputado>{
     * @Sigla       String                  Sigla da bancada (partido ou bloco partidário)
     * @Nome        String                  Nome da bancada
     * Lider        DeputadoLideranca       Deputado líder da bancada
     * Vice_Lider   LIST<DeputadoLideranca> Lista dos vice-líderes
     * }
     * <pre>
     */
    public static function obterLideresBancadas() {
        return self::obterData('ObterLideresBancadas');
    }

    /**
     * Retorna os partidos com representação na Câmara dos Deputados.
     * 
     * @return SimpleXMLObject Lista dos partidos com representação na Câmara dos Deputados.
     * <pre>
     * List<Partido>{
     * idPartido        String      ID do partido
     * siglaPartido     String      Sigla do partido
     * nomePartido      String      Nome do partido
     * dataCriacao      String      Data da criação do partido
     * dataExtincao     String      Data da extinção do partido
     * }
     * <pre>
     */
    public static function obterPartidosCD() {
        return self::obterData('ObterPartidosCD');
    }

    /**
     * Retorna os blocos parlamentares na Câmara dos Deputados.
     *
     * @param   string              $idBloco            ID do Bloco Parlamentar.
     * @param   int                 $numLegislatura     Número da Legislatura. Campo Vazio, legislatura atual. Apenas legislatura 53 em diante.
     * 
     * @return  SimpleXMLObject Lista os blocos na Câmara dos Deputados.
     * <pre>
     * List<Blocos>{
     * idBloco              String  ID do bloco
     * nomeBloco            String  Nome do bloco
     * siglaBloco           String  Sigla do bloco
     * dataCriacaoBloco     String  Data da criação do bloco
     * dataExtincaoBloco    String  Data da extinção do bloco
     * Partidos             String  List<Partido>
     * }
     * <pre>
     */
    public static function obterPartidosBlocoCD($post = array()) {
        return self::obterData('ObterPartidosBlocoCD', $post);
    }

    /**
     * Retorna os detalhes dos deputados da Câmara dos Deputados.
     *
     * @param  int $ideCadastro
     * @param  int $numLegislatura
     * 
     * @return SimpleXMLObject
     */
    public static function obterDetalhesDeputado($post = array()) {
        return self::obterData('ObterDetalhesDeputado', $post);
    }

}