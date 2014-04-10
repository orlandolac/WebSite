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
 * Controller padrão do sistema.
 * 
 * @category    Tools
 * @package     Pu
 * @copyright   Copyright (c) 2013-2014 OPovoUnido.com <http://opovounido.com>
 * @license     GNU GENERAL PUBLIC LICENSE - Version 3 <http://opovounido.com/LICENSE.txt>
 * 
 * @author      Alex Oliveira <bsi.alexoliveira@gmail.com>
 * @since       1.14.04.07
 * @version     1.14.04.07
 */
class Pu_Controller_Action extends Zend_Controller_Action{

    /**
     * Variável que formará a "trilha de migalhas" no layout.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.07
     * @version 1.14.04.07
     */
    public $trilha = array('<a href="/"><i class="glyphicon glyphicon-home"></i></a>');


    /**
     * Action padrão dos controllers do sistema.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.07
     * @version 1.14.04.07
     */
    public function indexAction() {
        
    }
    
    /**
     * Método que atribui variáveis a view após o "dispatch".
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.07
     * @version 1.14.04.07
     */
    public function postDispatch(){
        $this->view->trilha = $this->trilha;
    }
    
}