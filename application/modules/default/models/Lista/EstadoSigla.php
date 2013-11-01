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
final class Default_Model_Lista_EstadoSigla {

    public static function get($codigo = null) {
        if (is_null($codigo)) {
            return self::$lista;
        } else {
            if (isset(self::$lista[$codigo])) {
                return self::$lista[$codigo];
            } else {
                return null;
            }
        }
    }

    private static $lista = array(
        'NI' => array('0', ' '),
        'AC' => array('1', 'Acre'),
        'AL' => array('2', 'Alagoas'),
        'AM' => array('3', 'Amazonas'),
        'AP' => array('4', 'Amapá'),
        'BA' => array('5', 'Bahia'),
        'BR' => array('6', 'Brasil'),
        'CE' => array('7', 'Ceará'),
        'DF' => array('8', 'Distrito Federal'),
        'ES' => array('9', 'Espírito Santo'),
        'GO' => array('10', 'Goiás'),
        'MA' => array('11', 'Maranhão'),
        'MG' => array('12', 'Minas Gerais'),
        'MS' => array('13', 'Mato Grosso do Sul'),
        'MT' => array('14', 'Mato Grosso'),
        'PA' => array('15', 'Pará'),
        'PB' => array('16', 'Paraíba'),
        'PE' => array('17', 'Pernambuco'),
        'PI' => array('18', 'Piauí'),
        'PR' => array('19', 'Paraná'),
        'RJ' => array('20', 'Rio de Janeiro'),
        'RN' => array('21', 'Rio Grande do Norte'),
        'RO' => array('22', 'Rondônia'),
        'RR' => array('23', 'Roraima'),
        'RS' => array('24', 'Rio Grande do Sul'),
        'SC' => array('25', 'Santa Catarina'),
        'SE' => array('26', 'Sergipe'),
        'SP' => array('27', 'São Paulo'),
        'TO' => array('28', 'Tocantins'),
        'VT' => array('29', 'Voto em Trânsito'),
        'ZZ' => array('30', 'Exterior')
    );

}