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
final class Default_Model_Lista_Alfabeto {

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
         0 => '#',
         1 => 'A',
         2 => 'B',
         3 => 'C',
         4 => 'D',
         5 => 'E',
         6 => 'F',
         7 => 'G',
         8 => 'H',
         9 => 'I',
         10 => 'J',
         11 => 'K',
         12 => 'L',
         13 => 'M',
         14 => 'N',
         15 => 'O',
         16 => 'P',
         17 => 'Q',
         18 => 'R',
         19 => 'S',
         20 => 'T',
         21 => 'U',
         22 => 'V',
         23 => 'W',
         24 => 'X',
         25 => 'Y',
         26 => 'Z'
    );

}