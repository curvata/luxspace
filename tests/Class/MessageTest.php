<?php

namespace App\Tests\Entity;

use App\Class\Contact;
use App\Class\Message;
use App\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageTest extends KernelTestCase
{    
    public function testMessage()
    {
        // Obtenir
        
        $contact = new Contact();

        $message = new Message('test@test.be', 'sujet', '@template.twig', $contact);
        $this->assertStringContainsString(
            "test@test.be",
            $message->getMail());

        $this->assertStringContainsString(
            "sujet",
            $message->getSubject());

        $this->assertStringContainsString(
            "@template.twig",
            $message->getTemplate());

        $this->assertThat($contact,  $this->equalTo($message->getContent()));

        $message->setMail('blabla@test.be');
        $this->assertStringContainsString(
            "blabla@test.be",
            $message->getMail());
        
        $message->setSubject('sujet2');
        $this->assertStringContainsString(
            "sujet2",
            $message->getSubject());
        
        $message->setTemplate('@template2');
        $this->assertStringContainsString(
            "@template2",
            $message->getTemplate());
        
        $reservation = new Reservation();
        $message->setContent($reservation); 
        $this->assertThat($reservation,  $this->equalTo($message->getContent()));
    }
}