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
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected static $reg = null;

    protected function _initAutoload() {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Alex');
        $loader->setFallbackAutoloader(true);
    }

    protected function _initEncoding() {
        mb_internal_encoding("UTF-8");
    }

    protected function _initRegistro() {
        self::$reg = new Zend_Registry(array(), ArrayObject::ARRAY_AS_PROPS);
        Zend_Registry::setInstance(self::$reg);
    }

    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    protected function _initPlugins() {
        $app = $this->getApplication();
        if ($app instanceof Zend_Application) {
            $app = $this;
        }
        $app->bootstrap('FrontController');
        $front = $app->getResource('FrontController');
        $front->registerPlugin(new Alex_Layout());
        $app->bootstrap('Db');
        $db = $app->getResource('Db');
        Zend_Registry::set('db', $db);
    }
    
    protected function _initWebSite() {
        if(substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.'){
            header('Location: http://' . substr($_SERVER['HTTP_HOST'], 4));
        }
        
        define('DFL_NOME', 'O Povo Unido');
        define('DFL_URL', 'http://opovounido.com');
        define('DFL_MAIL_CONTATO', 'contato@opovounido.com');
        define('DFL_MAIL_NOREPLY', 'nao-responda@opovounido.com');
        
        define('ELEICAO_MAJORITAIA', '2010');
        define('ELEICAO_MUNICIPAL', '2012');
        
        $sys = new Zend_Session_Namespace('sistem');
        if (!($sys->dialogo instanceof Alex_Dialogo)) {
            $sys->dialogo = new Alex_Dialogo();
            $sys->dialogo->add('Infor', 'Seja Bem-Vindo !');
            $sys->dialogo->add('Alerta', 'Este projeto está em <b>fase de desenvolvimento</b>, isso quer dizer que ainda existem recursos incompletos, não testados ou com falhas. Tendo isso em mente, sinta-se a vontade para usá-lo.');
        }
        
        if(!($sys->localidade instanceof Alex_Localidade)){
            $sys->localidade = new Alex_Localidade();
        }
        
    }

}