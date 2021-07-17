<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{    
    public function testContactUserOffline()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Contact");
        $this->assertEquals(
            1,
            $crawler->filter('input[value]')->count());
    }
    
    public function testContactUserOnline()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        $this->assertEquals(
            4,
            $crawler->filter('input[value]')->count());
        $this->assertSelectorTextContains('h1', "Contact");
        $this->assertEquals(
            1,
            $crawler->filter('input[value="UnPrenom"]')->count());
        $this->assertEquals(
            1,
            $crawler->filter('input[value="UnNom"]')->count());
        $this->assertEquals(
            1,
            $crawler->filter('input[value="user1@user.be"]')->count());
    }

    /*public function testContactSendMail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Envoyer')->form([
            "contact[firstname]" => "Mike",
            "contact[lastname]" => "Menezes",
            "contact[email]" => "test@test.be",
            "contact[message]" => "Ceci est un message test du formulaire de contact"
        ]);
        $client->enableProfiler();
        $client->submit($form);
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
    }*/
}