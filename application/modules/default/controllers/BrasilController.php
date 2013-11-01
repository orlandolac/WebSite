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
class Default_BrasilController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/brasil">Brasil</a>';
        $this->view->get=$this->getRequest()->getParams();
    }
    
    public function indexAction() {
        $this->_redirect('/brasil/territorio');
    }
    
    public function territorioAction() {
        $this->local[] = '<a href="/brasil/territorio">Território</a>';
        $get = $this->getRequest()->getParams();
        $get['estado_sigla'] = mb_strtoupper($get['estado_sigla']);
        $get['include'] = 'territorio-pais';
        $uf = Default_Model_Lista_EstadoSigla::get($get['estado_sigla']);
        if(isset($uf[0]) && $uf[0]>0 && $uf[0]!=6 && $uf[0]<29){
            $get['include'] = 'territorio-estado';
            $get['estado_id'] = $uf[0];
            $get['estado_nome'] = $uf[1];
        }else{
            $get['estado_sigla'] = '';
        }
        if(strlen($get['estado_sigla'])==2){
            $this->local[] = '<a href="/brasil/territorio/' . mb_strtolower($get['estado_sigla']) . '">' . $get['estado_sigla'] . '</a>';
        }
        $this->view->get=$get;
    }

    public function eleicoesAction() {
        $this->local[] = '<a href="/brasil/eleicoes">Eleições</a>';
    }
    
    public function cargosAction() {
        $this->local[] = '<a href="/brasil/cargos">Cargos</a>';
    }
    
    public function leisAction() {
        $this->local[] = '<a href="/brasil/leis">Leis</a>';
    }
    
}