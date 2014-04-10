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
 * Classe que inicializa a aplicação com suas configurações e recursos.
 * 
 * @category    Bootstrap
 * @package     application
 * @copyright   Copyright (c) 2013-2014 OPovoUnido.com <http://opovounido.com>
 * @license     GNU GENERAL PUBLIC LICENSE - Version 3 <http://opovounido.com/LICENSE.txt>
 * 
 * @author      Alex Oliveira <bsi.alexoliveira@gmail.com>
 * @since       1.14.04.05
 * @version     1.14.04.05
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Variável que armazena o registro do sistema.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    protected static $registry = null;

    
    /**
     * Redireciona todas as requisições para endereços iniciados com "WWW"
     * para seu correspondente sem o "WWW".
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    protected function _initRedirectWWW() {
        if(substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.'){
            header('Location: http://' . substr($_SERVER['HTTP_HOST'], 4));
        }
    }
    
    
    /**
     * Inicializa o auto-carregamento de arquivos da aplicação.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    protected function _initAutoload() {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Pu');
        $loader->setFallbackAutoloader(true);
    }

    
    /**
     * Inicializa padrão de codigicação da aplicação.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    protected function _initEncoding() {
        mb_internal_encoding("UTF-8");
    }

    
    /**
     * Inicializa a variável estática $registry da aplicação.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    protected function _initRegistry() {
        self::$registry = new Zend_Registry(array(), ArrayObject::ARRAY_AS_PROPS);
        Zend_Registry::setInstance(self::$registry);
    }

    
    /**
     * Configura DocType das views da aplicação.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    
    /**
     * Inicializa alguns Plugins vitais do sistema.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    protected function _initPlugins() {
        $app = $this->getApplication();
        if ($app instanceof Zend_Application) {
            $app = $this;
        }
        $app->bootstrap('FrontController');
        $front = $app->getResource('FrontController');
        $front->registerPlugin(new Pu_Layout());
        $app->bootstrap('Db');
        $db = $app->getResource('Db');
        Zend_Registry::set('db', $db);
    }
    
    
    /**
     * Inicializa a sessão do sistema, juntamente com seus recursos.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    public function _initSession(){
        //$sess = new Zend_Session_Namespace('sess');
        //Pu_Session_Util::initLocation($sess);
    }
    
    
    /**
     * Inicializa diversas constantes que ficarão disponíveis para todo o
     * sistema.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    protected function _initWebSite() {
        define('SYS_NOME', 'O Povo Unido');
        define('SYS_NOME_HTML', 'ºPovo<b>Unido.com</b>');
        define('SYS_URL', 'http://opovounido.com');
        
        define('SYS_MAIL_CONTATO', 'contato@opovounido.com');
        define('SYS_MAIL_NOREPLY', 'nao-responda@opovounido.com');
        
        define('SYS_SOCIAL_FB_PAGE', 'http://www.facebook.com/opovounido.no.face');
        define('SYS_SOCIAL_FB_APP_ID', '484204754972513');
        define('SYS_SOCIAL_FB_SECRET', '2a2b335c0216801cb706c837533bba4c');
        
        define('SYS_SOCIAL_GP_PAGE', '//plus.google.com/100535975689010500813');
        define('SYS_SOCIAL_GP_PUBLISHER_ID', '100535975689010500813');
    }

}