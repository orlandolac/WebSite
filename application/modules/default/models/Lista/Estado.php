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
final class Default_Model_Lista_Estado {

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
        0  => array('  ', ' '),
        1  => array('AC', 'Acre'),
        2  => array('AL', 'Alagoas'),
        3  => array('AM', 'Amazonas'),
        4  => array('AP', 'Amapá'),
        5  => array('BA', 'Bahia'),
        6  => array('BR', 'Brasil'),
        7  => array('CE', 'Ceará'),
        8  => array('DF', 'Distrito Federal'),
        9  => array('ES', 'Espírito Santo'),
        10 => array('GO', 'Goiás'),
        11 => array('MA', 'Maranhão'),
        12 => array('MG', 'Minas Gerais'),
        13 => array('MS', 'Mato Grosso do Sul'),
        14 => array('MT', 'Mato Grosso'),
        15 => array('PA', 'Pará'),
        16 => array('PB', 'Paraíba'),
        17 => array('PE', 'Pernambuco'),
        18 => array('PI', 'Piauí'),
        19 => array('PR', 'Paraná'),
        20 => array('RJ', 'Rio de Janeiro'),
        21 => array('RN', 'Rio Grande do Norte'),
        22 => array('RO', 'Rondônia'),
        23 => array('RR', 'Roraima'),
        24 => array('RS', 'Rio Grande do Sul'),
        25 => array('SC', 'Santa Catarina'),
        26 => array('SE', 'Sergipe'),
        27 => array('SP', 'São Paulo'),
        28 => array('TO', 'Tocantins'),
        29 => array('VT', 'Voto em Trânsito'),
        30 => array('ZZ', 'Exterior')
    );

}