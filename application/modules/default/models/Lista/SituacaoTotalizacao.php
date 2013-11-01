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
final class Default_Model_Lista_SituacaoTotalizacao {

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
         1 => 'Eleito',
         2 => 'Suplente',
         3 => 'Renúncia / Falecimento / Cassação Antes da Eleição',
         4 => 'Não Eleito',
         5 => 'Média',
         6 => '2º Turno',
         7 => 'Renúncia / Falecimento / Cassação Após a Eleição',
         8 => 'Registro Negado Antes da Eleição',
         9 => 'Registro Negado Após a Eleição',
        10 => 'Substituído',
        11 => 'Indeferido com Recurso',
        12 => 'Cassado com Recurso',
        99 => 'Eleito por QP' // Quoeficiente Partidário! :( affs...
    );

}