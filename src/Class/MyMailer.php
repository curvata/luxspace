<?php

namespace App\Class;

use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;

class MyMailer
{
    private Request $request;
    private MailerInterface $mailer;

    public function __construct(RequestStack $request, MailerInterface $mailer)
    {
        $this->request = $request->getCurrentRequest();
        $this->mailer = $mailer;
    }
    
    /**
     * Envoyer un mail
     */
    public function send(Message $content): void
    {
        try {
            $email = (new TemplatedEmail())
                ->from($this->request->server->get('MY_MAIL'))
                ->to($content->getMail())
                ->subject($content->getSubject())
                ->htmlTemplate($content->getTemplate())
                ->context(
                    [
                        'content' => $content->getContent()
                        ]
                );
            $this->mailer->send($email);
        } catch (Exception $e) {
            throw new Exception("Impossible d'envoyer le mail (".$e.")");
        }
    }
}
