<?php
/**
 * Disponibiliza serviços de acesso aos dados das proposições que tramitaram ou que estão em
 * tramitação na Câmara dos Deputados.
 */
final class Camara_Proposicoes extends Camara_WebService {

    protected static $controller = 'Proposicoes.asmx';

    /**
     * Método para consultas personalizadas relacionadas as proposições.
     * 
     * @param  string $view
     * @param  string $post
     * @return SimpleXMLObject
     */
    public static function obterData($view, array $post = array()) {
        return parent::obterService(self::$controller, $view, $post);
    }
    
    /**
     * Retorna a lista de proposições que satisfaçam os critérios estabelecidos
     * 
     * @param string    Sigla                       Sigla do tipo de proposição
     * @param int       Numero                      Numero da proposição
     * @param int       Ano                         Ano da proposição
     * @param date      datApresentacaoIni          Menor data desejada para a data de apresentação da proposição. Formato: DD/MM/AAAA
     * @param date      datApresentacaoFim          Maior data desejada para a data de apresentação da proposição Formato: DD/MM/AAAA
     * @param int       IdTipoAutor                 Identificador do tipo de órgão autor da proposição, como obtido na chamada ao ListarTiposOrgao
     * @param string    ParteNomeAutor              Parte do nome do autor da proposição
     * @param string    SiglaPartidoAutor           Sigla do partido do autor da proposição
     * @param string    SiglaUfAutor                UF de representação do autor da proposição
     * @param string    GeneroAutor                 Gênero do autor (M - Masculino; F - Feminino; Default - Todos)
     * @param int       IdSituacaoProposicao        ID da situação da proposição
     * @param int       IdOrgaoSituacaoProposicao   ID do órgão de referência da situação da proposição
     * @param int       EmTramitacao                Indicador da situação de tramitação da proposição (1 - Em Tramitação no Congresso; 2- Tramitação Encerrada no Congresso; Default - Todas)
     * 
     * @return SimpleXMLObject Lista contendo as proposições que satisfazem os critérios estabelecidos
     * <pre>
     * List<Proposicao>{
     * Id                   Int                     ID da proposição
     * Nome                 String                  Nome da proposição
     * TipoProposicao       TipoProposicao          Dados do tipo da proposição
     * Numero               Int                     Número da proposição
     * Ano                  Int                     Ano de apresentação da proposição
     * OrgaoNumerador       OrgaoNumerador          Orgão onde a proposição numerada
     * DataApresentacao     Date                    Data de apresentação da proposição
     * Ementa               String                  Ementa da proposição
     * ExplicacaoEmenta     String                  Explicação da ementa da proposição
     * Regime               Regime                  Regime de tramitação da Proposição (ex: tramitação ordinária, urgência, etc)
     * Apreciacao           Apreciacao              Forma de apreciação da proposição na Câmara dos Deputados (conclusiva das comissões ou de apreciação do Plenário)
     * QtdeAutores          Int                     Quantidade de autores que subscreveram a proposição
     * Autor                Autor                   Primeiro autor da proposição
     * UltimoDespacho       UltimoDespacho          Último despacho proferido para a proposição
     * Situacao             Situacao                Situação da proposição na Câmara dos Deputados
     * ProposicaoPrincipal  ProposicaoPrincipal     Proposição a qual a proposição de referência está associada (apensada ou anexada)
     * }
     * </pre>
     */
    public static function listarProposicoes(array $post) {
        $data = array(
            'sigla'                 => ((isset($post['sigla']))?($post['sigla']):('')),
            'numero'                => ((isset($post['numero']))?($post['numero']):('')),
            'ano'                   => ((isset($post['ano']))?($post['ano']):('')),
            'datApresentacaoIni'    => ((isset($post['datApresentacaoIni']))?($post['datApresentacaoIni']):('')),
            'datApresentacaoFim'    => ((isset($post['datApresentacaoFim']))?($post['datApresentacaoFim']):('')),
            'autor'                 => ((isset($post['autor']))?($post['autor']):('')),
            'parteNomeAutor'        => ((isset($post['nomeAutor']))?($post['nomeAutor']):('')),
            'siglaPartidoAutor'     => ((isset($post['siglaPartidoAutor']))?($post['siglaPartidoAutor']):('')),
            'siglaUFAutor'          => ((isset($post['siglaUFAutor']))?($post['siglaUFAutor']):('')),
            'generoAutor'           => ((isset($post['generoAutor']))?($post['generoAutor']):('')),
            'codEstado'             => ((isset($post['codEstado']))?($post['codEstado']):('')),
            'codOrgaoEstado'        => ((isset($post['codOrgaoEstado']))?($post['codOrgaoEstado']):('')),
            'emTramitacao'          => ((isset($post['emTramitacao']))?($post['emTramitacao']):('')),
        );
        return self::obterData('ListarProposicoes', $data);
    }
    
    /**
     * Retorna a lista de proposições que satisfaçam os critérios estabelecidos
     * 
     * @param date      dtInicio
     * @param date      dtFim
     * 
     * @return SimpleXMLObject Lista contendo as proposições que satisfazem os critérios estabelecidos
     * <pre>
     * List<Proposicao>{
     * Id                   Int                     ID da proposição
     * Nome                 String                  Nome da proposição
     * TipoProposicao       TipoProposicao          Dados do tipo da proposição
     * Numero               Int                     Número da proposição
     * Ano                  Int                     Ano de apresentação da proposição
     * OrgaoNumerador       OrgaoNumerador          Orgão onde a proposição numerada
     * DataApresentacao     Date                    Data de apresentação da proposição
     * Ementa               String                  Ementa da proposição
     * ExplicacaoEmenta     String                  Explicação da ementa da proposição
     * Regime               Regime                  Regime de tramitação da Proposição (ex: tramitação ordinária, urgência, etc)
     * Apreciacao           Apreciacao              Forma de apreciação da proposição na Câmara dos Deputados (conclusiva das comissões ou de apreciação do Plenário)
     * QtdeAutores          Int                     Quantidade de autores que subscreveram a proposição
     * Autor                Autor                   Primeiro autor da proposição
     * UltimoDespacho       UltimoDespacho          Último despacho proferido para a proposição
     * Situacao             Situacao                Situação da proposição na Câmara dos Deputados
     * ProposicaoPrincipal  ProposicaoPrincipal     Proposição a qual a proposição de referência está associada (apensada ou anexada)
     * }
     * </pre>
     */
    public static function listarProposicoesTramitadasNoPeriodo($dataIni, $dataFim) {
        $data = array(
            'dtInicio'  => $datIni,
            'dtFim'     => $datFim,
        );
        return self::obterData('ListarProposicoesTramitadasNoPeriodo', $data);
    }
    
    /**
     * Retorna todas as proposições votadas em plenário num determinado período
     * 
     * @param       int     $ano
     * @param       string  $tipo
     * 
     * @return SimpleXMLObject Lista contendo todas as proposições que satisfazem os critérios estabelecidos
     * <pre>
     * List<Proposicao>{
     * Sigla        String          Sigla do Tipo da proposicao
     * Numero       Int             Numero da proposicao
     * Ano          Int             Ano de apresentação da proposição
     * Votacoes     LIST<Votacao>   Lista das votações nominais em Plenário da proposição
     * }
     * </pre>
     */
    public static function listarProposicoesVotadasEmPlenario($sigla, $ano) {
        $data = array(
            'sigla'     => $sigla,
            'ano'       => $ano,
        );
        return self::obterData('ListarProposicoesVotadasEmPlenario', $data);
    }
    
    /**
     * Retorna a lista de siglas de proposições
     * 
     * @return SimpleXMLObject Lista das siglas dos tipos de proposição.
     * <pre>
     * List<Sigla>{
     * @Sigla       String	Sigla do tipo da proposição (espécie da proposição)
     * @Descricao   String	Descrição do tipo da proposição (espécie da proposição)
     * @Ativa       String	Indica se é uma sigla de proposição (espécie da proposição) ativa (1= Ativa; 2=Inativa)
     * @Genero      String	Indicador do gênero da sigla da proposição (espécie da proposição)
     * }
     * </pre>
     */
    public static function listarSiglasTipoProposicao() {
        return self::obterData('ListarSiglasTipoProposicao');
    }
       
    /**
     * Retorna a lista de situações para proposições
     * 
     * @return SimpleXMLObject Lista dos tipos de situação das proposições.
     * <pre>
     * List<SituacaoProposicao>{
     * @ID          Int         ID da situação da proposição
     * @Descricao   String	Descrição da situação da proposição
     * @Ativa       String	Indica se é uma situação ativa (1= Ativa; 0=Inativa)
     * }
     * </pre>
     */
    public static function listarSituacoesProposicao() {
        return self::obterData('ListarSituacoesProposicao');
    }
        
    /**
     * Retorna a lista de tipos de autores das proposições
     *
     * @return SimpleXMLObject Lista dos tipos de autor das proposições.
     * <pre>
     * List<SituacaoProposicao>{
     * @ID          String	ID do tipo de autor
     * @Descricao   String	Descrição do tipo de autor
     * }
     * </pre>
     */
    public static function listarTiposAutores() {
        return self::obterData('ListarTiposAutores');
    }
    
    /**
     * Retorna os dados de uma determinada proposição a partir do tipo, número e ano
     *
     * @param       string  $tipo
     * @param       int     $numero
     * @param       int     $ano
     * 
     * @return SimpleXMLObject Dados da proposicao como número, ementa, autor, data de apresentação, etc.
     * <pre>
     * List<Proposicao>{
     * @Tipo                String              Tipo da proposicao
     * @Numero              Int                 Numero da proposicao
     * @Ano                 Int                 Ano de apresentação da proposição
     * IdProposicao         Int                 ID da proposição
     * Ementa               String              Ementa da proposição
     * ExplicacaoEmenta     String              Explicação da ementa da proposição
     * Autor                String              Nome do autor da proposição
     * DataApresentacao     Date                Data em que a propsoição foi apresentada na Câmara dos Deputados
     * RegimeTramitacao     String              Regime de tramitação da Proposição (ex: tramitação ordinária, urgência, etc)
     * UltimoDespacho       String              Ultimo despacho proferido para a proposição
     * Apreciacao           String              Forma de apreciação da proposição na Câmara dos Deputados (conclusiva das comissões ou de apreciação do Plenário)
     * Indexacao            String              Indexação (palavras-chave) associada à proposição
     * Situacao             String              Descrição da situação da proposição na Câmara dos Deputados
     * LinkInteiroTeor      String              URL contendo o link para o inteiro teor da proposição
     * apensadas            List<proposicao>	Proposições com assuntos semelhantes.
     * }
     * </pre>
     */
    public static function obterProposicao($sigla, $numero, $ano) {
        $data = array(
            'sigla'     => $sigla,
            'numero'    => $numero,
            'ano'       => $ano,
        );
        return self::obterData('ObterProposicao', $data);
    }
    
    /**
     * Retorna os dados de uma determinada proposição a partir do seu ID
     *
     * @param  int $IdProp
     * 
     * @return SimpleXMLObject Dados da proposicao como número, ementa, autor, data de apresentação, etc.
     * <pre>
     * List<Proposicao>{
     * @Tipo                String              Tipo da proposicao
     * @Numero              Int                 Numero da proposicao
     * @Ano                 Int                 Ano de apresentação da proposição
     * IdProposicao         Int                 ID da proposição
     * Ementa               String              Ementa da proposição
     * ExplicacaoEmenta     String              Explicação da ementa da proposição
     * Autor                String              Nome do autor da proposição
     * DataApresentacao     Date                Data em que a propsoição foi apresentada na Câmara dos Deputados
     * RegimeTramitacao     String              Regime de tramitação da Proposição (ex: tramitação ordinária, urgência, etc)
     * UltimoDespacho       String              Ultimo despacho proferido para a proposição
     * Apreciacao           String              Forma de apreciação da proposição na Câmara dos Deputados (conclusiva das comissões ou de apreciação do Plenário)
     * Indexacao            String              Indexação (palavras-chave) associada à proposição
     * Situacao             String              Descrição da situação da proposição na Câmara dos Deputados
     * LinkInteiroTeor      String              URL contendo o link para o inteiro teor da proposição
     * apensadas            List<proposicao>	Proposições com assuntos semelhantes.
     * }
     * </pre>
     */
    public static function obterProposicaoPorID($id) {
        return self::obterData('ObterProposicaoPorID', array('IdProp' => $id));
    }
    
    /**
     * Retorna os votos dos deputados a uma determinada proposição em votações ocorridas no Plenário da Câmara dos Deputados
     *
     * @param       string  $tipo
     * @param       int     $numero
     * @param       int     $ano
     * 
     * @return SimpleXMLObject Dados da votação da proposição
     * <pre>
     * List<Proposicao>{
     * Sigla        String          Sigla do Tipo da proposicao
     * Numero       Int             Numero da proposicao
     * Ano          Int             Ano de apresentação da proposição
     * Votacoes     LIST<Votacao>   Lista das votações nominais em Plenário da proposição
     * }
     * </pre>
     */
    public static function obterVotacaoProposicao($sigla, $numero, $ano) {
        $data = array(
            'tipo'     => $sigla,
            'numero'   => $numero,
            'ano'      => $ano,
        );
        return self::obterData('ObterVotacaoProposicao', $data);
    }
    
}