<?php
namespace Latihan\AnseraBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * @DI\Service("lat.service.mailer")
 *
 */
class MailerService
{
    protected $mailer;
    protected $templating;

    /**
     * @DI\InjectParams({
     *     "mailer" = @DI\Inject("mailer"),
     *     "templating" = @DI\Inject("templating")
     * })
     */
    public function __construct(\Swift_Mailer $mailer, TwigEngine $templating) {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * Send an email based on a raw html from the input parameter to one or more recipients
     * input :$recipients, $subject, $htmlBody, $from
	 * output : array("success"=>true);
     */
    public function sendTemplatelessEmail($recipients, $subject, $htmlBody, $from=array('noreply@freshgrad.com' => 'FreshGrad')) {

        $message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom($from)
        ->setTo((array)$recipients)
        ->setBody($htmlBody);

        $message->addPart($htmlBody, 'text/html');

        $this->mailer->send($message);
		 return array("success"=>true);
    }



    /**
     * Send an email based on a template to one or more recipients
     * input : recipients, subject, templateName, templateVars
	 * output :array("success"=>true,
					 "plainTextBody"=>$plainTextBody
					);
     */
    public function sendEmail($recipients, $subject, $templateName, $templateVars = array(), $from=array('noreply@freshgrad.com' => 'FreshPath'),$attachFile="") {
	
	
        $textTemplateName = null;
        $htmlTemplateName = null;
        if (is_array($templateName)) {
            if(array_key_exists('text', $templateName)) {
                $textTemplateName = $templateName['text'];
            }
            if(array_key_exists('html', $templateName)) {
                $htmlTemplateName = $templateName['html'];
            }
        } else {
            $textTemplateName = $templateName;
        }

        if ($textTemplateName == null) {
            throw new \RuntimeException("No plaintext template given.");
        }

        $plainTextBody = $this->templating->render($textTemplateName, $templateVars);
			
        $message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom($from)
        ->setTo((array)$recipients)
        ->setBody($plainTextBody);
		
		if(!empty($attachFile)){
			$message->attach(\Swift_Attachment::fromPath($attachFile));
		}   
 
        if ($htmlTemplateName != null) {			
            $htmlBody = $this->templating->render($htmlTemplateName, $templateVars);
            $message->addPart($htmlBody, 'text/html');
        }

        $send=$this->mailer->send($message);
						
		return array("success"=>true,
					 "plainTextBody"=>$plainTextBody
					); 
    }

}