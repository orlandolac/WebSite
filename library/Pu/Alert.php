<?php
/* Copyright 2013-2014 de OPovoUnido.com.
 * 
 * Este arquivo é parte do programa OPovoUnido.com. O OPovoUnido.com é um
 * software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da [GNU General Public License OU GNU Affero General Public License] como
 * publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença.
 * 
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a licença para maiores detalhes.
 * 
 * Você deve ter recebido uma cópia da [GNU General Public License OU GNU Affero
 * General Public License], sob o título "LICENCA.txt", junto com este programa, se
 * não, acesse http://www.gnu.org/licenses/.
 */

/**
 * Classe utilizada para agrupar e imprimir alertas do sistema para o usuário.
 * 
 * @category    Tools
 * @package     Pu
 * @copyright   Copyright (c) 2013-2014 OPovoUnido.com <http://opovounido.com>
 * @license     GNU GENERAL PUBLIC LICENSE - Version 3 <http://opovounido.com/LICENSE.txt>
 * 
 * @author      Alex Oliveira <bsi.alexoliveira@gmail.com>
 * @since       1.14.04.05
 * @version     1.14.04.05
 */
class Pu_Alert{

    /**
     * Constantes da classe que determinam os tipos de alertas.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    public static $INFOR = 'alert-info';
    public static $SUCCESS = 'alert-success';
    public static $DANGER = 'alert-danger';
    public static $WARNING = 'alert-warning';
    
    
    /**
     * Retorna uma referência para os alertas da sessão.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    public static function get(){
        $sess = new Zend_Session_Namespace('sess');
        if (!($sess->alerts instanceof ArrayObject)) {
            $sess->alerts = new ArrayObject();
        }
        return $sess->alerts;
    }
    
    
    /**
     * Adiciona um novo alerta a sessão.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     * 
     * @param   string $message Texto da mensagem.
     * @param   string $class Classe que determina o tipo de mensagem.
     * @param   array $data Atributos a serem incluídos no HTML da mensagem.
     */
    public static function add($message, $class = 'alert-info', array $data = array()) {
        $alerts = self::get();
        $alerts[] = array('class' => $class, 'message' => $message, 'data' => $data);
    }

    
    /**
     * Adiciona uma lista de alertas a sessão.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     * 
     * @param array $alertsList Lista de alertas a serem adicionados
     */
    public static function addList(array $alertsList) {
        foreach ($alertsList as $alert) {
            if(empty($alert['class']) || empty($alert['message'])){
                continue;
            }
            self::add($alert['class'], $alert['message'], (empty($alert['data']) ? $alert['data'] : array()));
        }
    }
    
    
    /**
     * Imprimi todos os alertas disponíveis.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    public static function show() {
        $alerts = self::get();
        foreach ($alerts as $alert) {
            echo '<div class="alert ' . $alert['class'] . ' alert-dismissable" ' . implode(' ', $alert['data']) . ' ><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' . $alert['message'] . '</div>';
        }
        self::cls();
    }

    
    /**
     * Limpa a lista de alertas disponíveis.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    public static function cls() {
        $sess = new Zend_Session_Namespace('sess');
        $sess->alerts = new ArrayObject();
    }
    
    
    /**
     * Retorna um determinado alerta através da chave do array.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     * 
     * @return int Indice do array
     */
    public function getByKey($key) {
        $alerts = self::get();
        if(array_key_exists($key, $alerts)){
            return $alerts[$key];
        }else{
            return null;
        }
    }
    
}