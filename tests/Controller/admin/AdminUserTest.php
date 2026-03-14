<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminUserTest extends WebTestCase
{     
    public function testAdminUser()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $client->request('GET', '/admin/user');
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Utilisateurs');
        $this->assertEquals(1, $crawler->filter('h1 .count_badge')->count());
    } 

    public function testAdminUserNew()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/user/new');
        $this->assertSelectorTextContains('h1', 'Créer un utilisateur');
    } 

    public function testAdminUserShow()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/user/1');
        $this->assertSelectorTextContains('h1', 'Utilisateur (UnNom UnPrenom)');
        $this->assertEquals(
            1,
            $crawler->filter('tr:contains("user1@user.be")')->count());
    }
}