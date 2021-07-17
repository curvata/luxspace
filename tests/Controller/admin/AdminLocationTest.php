<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminLocationTest extends WebTestCase
{     
    public function testAdminLocation()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/location');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Destinations (6)');
    } 

    public function testAdminLocationNew()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/location/new');
        $this->assertSelectorTextContains('h1', 'Créer une nouvelle destination');
    } 

    public function testAdminLocationEdit()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/location/1/edit');
        $form = $crawler->selectButton('Sauvegarder')->form();
        $form['location[title]'] = 'MarsEdit';
        $client->submit($form);
        $locationRepository = static::$container->get(LocationRepository::class);
        $this->assertStringContainsString('MarsEdit', $locationRepository->find(1)->getTitle());
    }

    public function testAdminLocationShow()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/location/1');
        $this->assertSelectorTextContains('h1', 'Destination (Mars)');
        $this->assertEquals(
            1,
            $crawler->filter('tr:contains("Petite description de la planète Mars")')->count());
    }
}