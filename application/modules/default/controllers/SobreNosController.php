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
class Default_SobreNosController extends Alex_Controller {

    public function init() {
        parent::init();
        $this->local[] = '<a href="/sobre-nos">Sobre nós</a>';
    }

    public function indexAction() {
        $this->_redirect('/sobre-nos/quem-somos');
    }

    public function quemSomosAction() {
        $this->local[] = '<a href="/sobre-nos/quem-somos">Quem Somos</a>';
    }

    public function conhecaOProjetoAction() {
        $this->local[] = '<a href="/sobre-nos/conheca-o-projeto">CONHEÇA o Projeto !</a>';
    }

    public function facaSuaDoacaoAction() {
        $this->local[] = '<a href="/sobre-nos/faca-sua-doacao">Faça sua DOAÇÃO !</a>';
    }

    public function comoAjudarAction() {
        $this->local[] = '<a href="/sobre-nos/como-ajudar">Como AJUDAR ?</a>';
    }

    public function politicasETermosAction() {
        $this->local[] = '<a href="/sobre-nos/politicas-e-termos">Políticas e Termos</a>';
    }

    public function recomendamosAction() {
        $this->local[] = '<a href="/sobre-nos/recomendamos">Recomendamos</a>';
    }

    public function perguntasFrequentesAction() {
        $this->local[] = '<a href="/sobre-nos/perguntas-frequentes">Perguntas Frequentes</a>';
    }

}