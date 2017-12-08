<?php

namespace MySDK\Service;

use Zend\Mail\Transport\File as FileTransport;
use Zend\Mail\Transport\FileOptions;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime as Mime;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

/**
 * Classe para controle dos serviços de e-mails do projeto utilizando as
 * configurações do Zend_Mail.
 */
class Mail {

    const K_BODY_TYPE_PLAIN = 'plain';
    const K_BODY_TYPE_HTML = 'html';
    const K_BODY_TYPE_IMAGE = 'image';

    /**
     * @var Zend\Mail\Transport\File|Zend\Mail\Transport\Smtp
     */
    private $_adapter;

    /**
     * @var Zend\Mail\Message
     */
    private $_message;

    /**
     * @var array
     */
    private $_files = array();

    /**
     * Construtor da classe.
     *
     * Ao instanciar a classe é criada uma instância de Zend\Mail\Message na
     * variável declarada para isso afim de setarmos as configurações do e-mail
     * posteriormente, além de definirmos o adapter (tipo de serviço de envio
     * de e-mail) para ser utilizado (SMTP ou FILE, quando em debug).
     *
     * @param boolean $debug indica se o envio é de debug ou não.
     *
     * @return void
     */
    public function __construct() {
        $this->_message = new Message();
        $this->_message->setEncoding("UTF-8");

        $this->setAdapter(AbstractService::getServiceManager()->get('Config'));
    }

    /**
     * Configurando o adapter padrão que será utilizado para enviar o e-mail.
     *
     * @todo fazer a contiguração SMTP.
     *
     * @param boolean $debug indica se o envio é de debug ou não.
     *
     * @return void
     */
    public function setAdapter($config = array('debug' => true)) {
        //recupera a configuracao
        if ($config['mail']["debug"] === true) {
            // Setup File transport
            $transport = new \Zend\Mail\Transport\File();
            $options = new \Zend\Mail\Transport\FileOptions($config['mail']['file_options']);
            $transport->setOptions($options);
        } else {
            $transport = new SmtpTransport;
            $options = new SmtpOptions($config['mail']['smtp_options']);
            $transport->setOptions($options);
        }
        $this->_adapter = $transport;
    }

    /**
     * Seta o remetente do e-mail.
     *
     * Para setar mais de um remetente para a mensagem, basta chamar este
     * método o número de remetentes existentes passando por parâmetro os dados
     * de cada um.
     *
     * @param string $from e-mail do remetente.
     * @param string $name nome do remetente.
     *
     * @return void
     */
    public function setFrom($from, $name) {
        $this->_message->addFrom($from, $name);
    }

    /**
     * Seta o destinatário do e-mail.
     *
     * Para setar mais de um destinatário para a mensagem, basta chamar este
     * método o número de destinatários existentes passando por parâmetro os
     * dados de cada um.
     *
     * @param string $from e-mail do destinatário.
     * @param string $name nome do destinatário.
     *
     * @return void
     */
    public function setTo($to) {
        $this->_message->addTo($to);
    }

    public function setBcc($bcc) {
        $this->_message->addBcc($bcc);
    }

    /**
     * Seta o assunto do e-mail.
     *
     * @param string $subject assunto do e-mail.
     *
     * @return void
     */
    public function setSubject($subject) {
        $this->_message->setSubject($subject);
    }

    /**
     * Seta o corpo do e-mail.
     *
     * @param string $message corpo do e-mail.
     *
     * @return void
     */
    public function setMessage($message) {
        $html = new MimePart($message);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));

        $this->_message->setBody($body);
    }

    /**
     * Seta o corpo do e-mail e anexa os arquivos enviados no e-mail.
     *
     * @param string $message mensagem do corpo do e-mail.
     * @param array  $files   array de files para anexar no e-mail.
     *
     * @return void
     */
    public function setMessageAndFiles($message, Array $files) {
        $arrParts = array();

        $html = new MimePart($message);
        $html->type = "text/html";
        $arrParts[] = $html;

        foreach ($files as $file) {
            if ($file['content']) {
                $attachment = new MimePart($file['content']);
                $attachment->type = $file['type'];
                $attachment->filename = $file['name'];
                $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
                // Setting the encoding is recommended for binary data
                $attachment->encoding = Mime::ENCODING_BASE64;
                $arrParts[] = $attachment;
            } else {
                throw new \Base\Service\Exception\ServiceException('Erro ao anexar o arquivo ' . $file['name'] . '. <br/> Arquivo pode está corrompido.');
            }
        }

        $body = new MimeMessage();
        $body->setParts($arrParts);

        $this->_message->setBody($body);
    }

    /**
     * Dispara o e-mail.
     *
     * @return boolean
     */
    public function send() {
        return $this->_adapter->send($this->_message);
    }

}
