<?php
/* Copyright 2013 de OPovoUnido.com.
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
 * General Public License], sob o título "LICENCA", junto com este programa, se
 * não, acesse http://www.gnu.org/licenses/.
 */

/**
 * Define que todos os erros sejam apresentados durante a execução do script. A
 * Modificação será desfeita ao fim da processo. Esta configuração NÃO será
 * utilizada na versão em produção do sistema.
 * 
 * @author Alex Oliveira <bsi.alexoliveira@gmail.com>
 */
ini_set("display_errors", E_ALL);

/**
 * Define uma constante disponível em todo o sistema com o caminho para o
 * diretório raiz do sistema. Esta constande deve ser utilizada sempre que for
 * necessário especificar o caminho completo de algun arquivo ou diretório.
 * 
 * @author Alex Oliveira <bsi.alexoliveira@gmail.com>
 */
defined('SYS_PATH') || define('SYS_PATH', realpath(dirname(__FILE__) . '/../'));

/**
 * Define uma constante disponível em todo o sistema com o ambiente no qual o
 * sistema está sendo executado. Poderá assumir os valore: "development" para
 * versão em Desenvolvimento; "homologation" para versão em Homoligação e
 * "production" para versão em Produção. Utilize esta contante para criar
 * recursos que devem ser executados apenas em um determinado ambienete.
 * 
 * @author Alex Oliveira <bsi.alexoliveira@gmail.com>
 */
defined('SYS_ENV') || define('SYS_ENV', (getenv('SYS_ENV') ? getenv('SYS_ENV') : 'development'));

/**
 * Define os diretório padrão para include do sistema. Por razões de desempenho
 * esta instrução não deve ser alterada.
 * 
 * @author Alex Oliveira <bsi.alexoliveira@gmail.com>
 */
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(SYS_PATH . '/library'),
    get_include_path()
)));

/**
 * Importa o arquivo Zend/Application.php, cria e executa a aplicação com as
 * devidas configurações. A partir da execução da aplicação todas as bibliotecas
 * estarão disponíveis sem a necesidade de mais include.
 * 
 * @author Alex Oliveira <bsi.alexoliveira@gmail.com>
 */
require_once SYS_PATH . '/library/Zend/Application.php';
$application = new Zend_Application(SYS_ENV, SYS_PATH . '/application/Config.ini');
$application->bootstrap()->run();