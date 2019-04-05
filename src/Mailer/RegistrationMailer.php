<?php


namespace App\Mailer;

use App\Entity\User;
use Twig\Environment;

class RegistrationMailer
{
    private $mailer;
    private $twig;
    private $subject;
    private $sender;
    private $txtTemplate;
    private $htmlTemplate;

    public function __construct (
        \Swift_Mailer $mailer,
        Environment $twig,
        string $subject,
        string $sender,
        string $txtTemplate,
        string  $htmlTemplate
    )
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->subject = $subject;
        $this->sender = $sender;
        $this->txtTemplate = $txtTemplate;
        $this->htmlTemplate = $htmlTemplate;
    }

    public function sendMail(User $user)
    {
        $message = new \Swift_Message();
        //Fil mail subject
        $message->setSubject($this->subject);
            //A parameter
        //Fil mail sender
        $message->setFrom($this->sender);
            //A parameter
        //Fil mail recipient
        $message->setTo($user->getEmail());
            //Coming from user object
        //Fil mail content
        $message->setBody($this->twig->render($this->htmlTemplate, ['user' => $user]), 'text/html');
        $message->addPart($this->twig->render($this->txtTemplate, ['user' => $user]), 'text/plain');
            //A template (twig)
        //Fil mail mail
            //With template (twig)
        //Send the email
       $this->mailer->send($message);
    }
}