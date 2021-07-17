<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminPictureTest extends WebTestCase
{     
    public function testAdminDeparture()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $client->request('GET', '/admin/pictures/location/1');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('1', $data[0][0]);
        $this->assertStringContainsString('small_mars.jpeg', $data[0][1]);
    } 
}