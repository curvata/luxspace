<?php

namespace App\Tests\Class;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MailTest extends WebTestCase
{    
    public function testMailIsSentAndContentIsOk()
    {
        $client = static::createClient([], ['MY_MAIL' => 'test@test.be']);
        $crawler = $client->request('GET', '/contact');
        $form = $crawler->selectButton('contact[submit]')->form();

        $crawler = $client->submit($form, [
            'contact[firstname]' => 'un super prenom',
            'contact[lastname]' => 'un super nom',
            'contact[email]' => 'toto@tata.be',
            'contact[message]' => 'Bonjour, ceci est un message depuis le site internet !'
        ]);
        $this->assertEmailCount(1);
        $this->assertSelectorTextContains('.alert_success', 'Votre message a bien été envoyé');
    }
}