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
final class Default_Model_Mail extends Zend_Mail{

    public function __construct($charset = null) {
        parent::__construct($charset);
        Zend_Mail::setDefaultFrom(DFL_MAIL_NOREPLY, DFL_NOME);
    }
    
    public function send($transport = null) {
        if($_SERVER['SERVER_ADDR'] != '127.0.0.1'){
            return parent::send($transport);
        }else{
            return false;
        }
    }
    
    public function enviar($nome, $email, $assunto, $mensagem, $user=0) {
        $this->addTo($email, utf8_decode($nome));
        $this->setSubject(utf8_decode($assunto));
        if($user > 0){
            // Coloca dados do atendente
            $this->setFrom(DFL_MAIL_NOREPLY, DFL_NOME);
        }else{
            $this->setFrom(DFL_MAIL_NOREPLY, DFL_NOME);
        }
        
        $this->setMensagem($mensagem);
        return $this->send();
    }
    
    public function setMensagem($mensagem) {
        ob_start();
            ?>
            <div style="background-color:#005500;background-image:url('<?php //echo DFL_URL ?>/temas/default/default/imagens/logo.png');background-position:top center;border:2px double #020">
                <div style="padding:10px">
                    <a href="<?php echo DFL_URL ?>"><img src="<?php echo DFL_URL ?>/temas/default/default/imagens/logo.png" height="70" border="0"></a>
                </div>
                <div>
                    <div style="padding:13px;background-color:#fff;background-image:url('<?php echo DFL_URL ?>/temas/default/default/imagens/fundoQuadrado.png');">
                        <?php echo $mensagem ?>
                    </div>
                    <div style="padding:10px 13px;background:#f1f1f1">
                        <b style="font-size: 15px">OPovoUnido.com</b> <i>jamais será vencido!</i>
                    </div>
                </div>
                <div style="padding:10px 10px 8px 4px">
                    <a style="display:inline-block;padding:5px 10px;margin-right:10px;color:#fff;font-size:15px;font-weight:bold" href="<?php echo DFL_URL ?>/brasil">BRASIL</a>
                    <a style="display:inline-block;padding:5px 10px;margin-right:10px;color:#fff;font-size:15px;font-weight:bold" href="<?php echo DFL_URL ?>/candidatos">CANDIDATOS</a>
                    <a style="display:inline-block;padding:5px 10px;margin-right:10px;color:#fff;font-size:15px;font-weight:bold" href="<?php echo DFL_URL ?>/dossies">DOSSIÊS</a>
                    <a style="display:inline-block;padding:5px 10px;margin-right:10px;color:#fff;font-size:15px;font-weight:bold" href="<?php echo DFL_URL ?>/partidos">PARTIDOS</a>
                    <a style="display:inline-block;padding:5px 10px;margin-right:10px;color:#fff;font-size:15px;font-weight:bold" href="<?php echo DFL_URL ?>/projetos">PROJETOS</a>
                    <a style="display:inline-block;padding:5px 10px;margin-right:10px;color:#fff;font-size:15px;font-weight:bold" href="<?php echo DFL_URL ?>/noticias">NOTÍCIAS</a>
                </div>
            </div>
            <?php
            $saida = ob_get_contents();
        ob_end_clean();
        $this->setBodyHtml(utf8_decode($saida));
        
        if($_SERVER['SERVER_ADDR'] == '127.0.0.1'){
            echo $saida;
        }
    }
    
}