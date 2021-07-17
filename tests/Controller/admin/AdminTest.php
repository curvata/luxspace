<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminTest extends WebTestCase
{     
    public function testAdminUserOffline()
    {
        $client = static::createClient();
        $client->request('GET', '/admin');
        $response = $client->getResponse();
        $this->assertEquals('/connexion', $response->headers->get('location'));
    } 

    public function testAdminSimpleUserOnline()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user2@user.be');
        $client->loginUser($testUser);
        $client->request('GET', '/admin');
        $response = $client->getResponse();
        $this->assertEquals('403', $response->getStatusCode());
    } 

    public function testAdminAdminOnline()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin');
        $this->assertSelectorTextContains('h1', 'Administration');
    } 
}