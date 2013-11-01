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
final class Default_Model_Lista_Cargo {

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
         0 => ' ',
         1 => 'Presidente',         // Majoritária
         2 => 'Vice-Presidente',    // Majoritária
         3 => 'Governador',         // Majoritária
         4 => 'Vice-Governador',    // Majoritária
         5 => 'Senador',            // Majoritária
         6 => 'Deputado Federal',   // Majoritária
         7 => 'Deputado Estadual',  // Majoritária
         8 => 'Deputado Distrital', // Majoritária
         9 => '1º Suplente Senador',// Majoritária
        10 => '2º Suplente Senador',// Majoritária
        11 => 'Prefeito',           // Municipal
        12 => 'Vice-Prefeito',      // Municipal
        13 => 'Vereador'            // Municipal
    );

}