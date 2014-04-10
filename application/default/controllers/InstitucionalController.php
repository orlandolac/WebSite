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
 * Controller institucional do módulo "default".
 * 
 * @category    Controller
 * @package     application/default/controller
 * @copyright   Copyright (c) 2013-2014 OPovoUnido.com <http://opovounido.com>
 * @license     GNU GENERAL PUBLIC LICENSE - Version 3 <http://opovounido.com/LICENSE.txt>
 * 
 * @author      Alex Oliveira <bsi.alexoliveira@gmail.com>
 * @since       1.14.04.05
 * @version     1.14.04.05
 */
class Default_InstitucionalController extends Pu_Controller_Action {

    public function init(){
        parent::init();
        $this->trilha[] = '<a href="/institucional">Institucional</a>';
    }
    
    public function indexAction(){
        $this->_redirect('/institucional/quem-somos');
    }
    
    public function quemSomosAction(){
        $this->trilha[] = '<a href="/institucional/quem-somos">Quem-Somos</a>';
    }
    
    public function conhecaOProjetoAction(){
        $this->trilha[] = '<a href="/institucional/conheca-o-projeto">CONHEÇA o Projeto !</a>';
    }
    
    public function comoAjudarAction(){
        $this->trilha[] = '<a href="/institucional/como-ajudar">Como AJUDA ?</a>';
    }
    
    public function perguntasFrequentesAction(){
        $this->trilha[] = '<a href="/institucional/perguntas-frequentes">Perguntas Frequentes</a>';
    }
    
    public function politicasETermosAction(){
        $this->trilha[] = '<a href="/institucional/politicas-e-termos">Políticas & Termos</a>';
    }
    
    public function recomendamosAction(){
        $this->trilha[] = '<a href="/institucional/recomendamos">Recomendamos</a>';
    }

}