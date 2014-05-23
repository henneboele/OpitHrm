<?php

/*
 *  This file is part of the {Bundle}.
 * 
 *  (c) Opit Consulting Kft. <info@opit.hu>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\Component\Email;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;

/**
 * Description of EmailManager
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 */
class EmailManager
{
    protected $swiftMailer;
    protected $templating;
    protected $logger;
    protected $config;


    private $subject;
    private $mailBody;
    private $recipient;
    private $emailFormat = 'text';
    private $from;
    
    /**
     * Constructor of email manager component.
     * 
     * @param \Swift_Mailer $swiftMailer
     * @param \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine $templating
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $config
     */
    public function __construct(\Swift_Mailer $swiftMailer, TimedTwigEngine $templating, LoggerInterface $logger = null, array $config = array())
    {
        $this->swiftMailer = $swiftMailer;
        $this->templating = $templating;
        $this->logger = $logger;
        $this->config = $config;
        
        $this->validateConfig();
    }
    
    /**
     * Method to validate if mail_sender is in config.
     * 
     * @throws Exception\ConfigurationException
     */
    protected function validateConfig()
    {
        if (!array_key_exists('mail_sender', $this->config)) {
            throw new Exception\ConfigurationException('No mail sender found in config.');
        }
    }
    
    /**
     * Method to set mail subject.
     * 
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
    
    /**
     * Method to set mail recipient.
     * 
     * @param string $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Method to set body template for mail.
     * 
     * @param string $template
     * @param array $templateVars
     */
    public function setBodyByTemplate($template, array $templateVars = array())
    {
        if (strpos($template, '.html.twig')) {
            $this->emailFormat = 'text/html';
        }
        $this->mailBody = $this->templating->render($template, array('templateVars' => $templateVars));
        
        if (null !== $this->logger) {
            $this->logger->info('[EmailManager] Mail body set by template.');
        }
    }
    
    /**
     * Sends an email with given parameters
     * Content can be explicitly set and overwrites base template settings
     * 
     * @param string $content
     * @throws \RuntimeException
     */
    public function sendMail($content = null)
    {
        if (null !== $content) {
            $this->mailBody = $content;
        }
        if (!$this->mailBody) {
            throw new \RuntimeException('A mail body must be present!');
        }
        
        // create email to send, render template for email
        $message = \Swift_Message::newInstance()
            ->setSubject($this->subject)
            ->setFrom($this->config['mail_sender'])
            ->setTo($this->recipient)
            ->setBody($this->mailBody, $this->emailFormat);
        
        // send the message
        $result = $this->swiftMailer->send($message);
        
        if (null !== $this->logger) {
            $this->logger->info('[EmailManager] Email sent.');
        }
        
        return $result;
    }
}