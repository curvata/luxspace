<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\ReservationFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{    
    public function testLoginUserOffline()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');
        $this->assertResponseIsSuccessful();
        $this->assertEquals(
            3,
            $crawler->filter('input')->count());
        $this->assertSelectorTextContains('h1', "Se connecter");
    }

    public function testLoginUserOnline()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/connexion');
        $response = $client->getResponse();
        $this->assertEquals('/compte', $response->headers->get('location'));
    }
}