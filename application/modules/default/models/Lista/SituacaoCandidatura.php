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
final class Default_Model_Lista_SituacaoCandidatura {

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
      // 1 => '',
         2 => 'Deferido',
         3 => 'Indeferido',
         4 => 'Indeferido com Recurso',
         5 => 'Cancelado',
         6 => 'Renúncia',
         7 => 'Falecido',
         8 => 'Aguardando Julgamento',
         9 => 'Inelegível',
        10 => 'Cassado',
        11 => 'Impugnado',
      //12 => '',
        13 => 'Não Conhecimento do Pedido',
        14 => 'Indeferido',
      //15 => '',
        16 => 'Deferido com Recurso',
        17 => 'Substituto Pendente de Julgamento',
        18 => 'Cassado com Recurso'
    );

}