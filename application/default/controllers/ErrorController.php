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
 * Classe que implementa o Controller para falhas do sistema.
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
class Default_ErrorController extends Pu_Controller_Action {

    /**
     * Action para apresentação de falhas ao usuário e registro de logs.
     * 
     * @author  Alex Oliveira <bsi.alexoliveira@gmail.com>
     * @since   1.14.04.05
     * @version 1.14.04.05
     */
    public function errorAction() {
        $this->trilha[] = 'Registro de Exceção';
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'Ocorreu uma exceção.';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:{
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'A página solicitada não foi encontrada.';
                break;
            }
            default:{
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Ocorreu uma exceção.';
                break;
            }
        }

        if (($log = $this->getLog())) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Parâmetros da Requisição', $priority, $errors->request->getParams());
        }

        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }else{
            $this->registryLog($errors);
        }
        
        $this->view->request = $errors->request;
    }

    public function getLog() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
    
    private function registryLog($errors){
        $log = explode(' ', substr(microtime(), 2));
        $log = time() . '.' . $log[0];

        $log = fopen(SYS_PATH . '/application/logs/' . $log . '.log', 'a');
        fwrite($log, 'Hora......: ' . date('H:i:s') . "\n");
        fwrite($log, 'Data......: ' . date('d-m-Y') . "\n");
        fwrite($log, 'Mensagem..: ' . $errors->exception->getMessage() . "\n");
        fwrite($log, 'Módulo....: ' . $errors->request->getParam('module') . "\n");
        fwrite($log, 'Versão....: 1.0 Beta' . "\n");
        fwrite($log, 'Arquivo...: ' . $errors->exception->getFile() . "\n");
        fwrite($log, 'Linha.....: ' . $errors->exception->getLine() . "\n");
        fwrite($log, 'Codigo....: ' . $errors->exception->getCode());
        fwrite($log, "\n\nPARÂMETROS DA REQUISIÇÃO:\n");
        
        ob_start();
        echo var_export($errors->request->getParams(), true);
        $tmp = ob_get_contents();
        ob_end_clean();
        fwrite($log, $tmp);
        
        fwrite($log, "\n\nRASTREAMENTO:\n");
        ob_start();
        echo $errors->exception->getTraceAsString();
        $tmp = ob_get_contents();
        ob_end_clean();
        fwrite($log, $tmp);

        fwrite($log, "\n\nCACHE:\n");
        ob_start();
        //print_r($errors->exception->getTrace());
        $tmp = ob_get_contents();
        ob_end_clean();
        fwrite($log, $tmp);

        fclose($log);
    }

}