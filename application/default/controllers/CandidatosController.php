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
 * Controller de candidatos do módulo "default".
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
class Default_CandidatosController extends Pu_Controller_Action {

    public function init(){
        parent::init();
        $this->trilha[] = '<a href="/candidatos">Candidatos</a>';
    }
    
    public function indexAction(){
        
    }
    
    public function presidentesAction(){
        $this->trilha[] = '<a href="/candidatos/presidentes">Presidentes</a>';
    }
    
    public function deputadosFederaisAction(){
        $this->trilha[] = '<a href="/candidatos/deputados-federais">Deputados Federais</a>';
    }
    
    public function senadoresAction(){
        $this->trilha[] = '<a href="/candidatos/senadores">Senadores</a>';
    }
    
    public function governadoresAction(){
        $this->trilha[] = '<a href="/candidatos/governadores">Governadores</a>';
    }
    
    public function deputadosDistritaisAction(){
        $this->trilha[] = '<a href="/candidatos/deputados-distritais">Deputados Distritais</a>';
    }
    
    public function deputadosEstaduaisAction(){
        $this->trilha[] = '<a href="/candidatos/deputados-estaduais">Deputados Estaduais</a>';
    }
    
    public function prefeitosAction(){
        $this->trilha[] = '<a href="/candidatos/prefeitos">Prefeitos</a>';
    }
    
    public function vereadoresAction(){
        $this->trilha[] = '<a href="/candidatos/vereadores">Vereadores</a>';
    }

}