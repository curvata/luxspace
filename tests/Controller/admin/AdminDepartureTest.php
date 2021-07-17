<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Repository\DepartureRepository;
use App\Repository\LocationRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminDepartureTest extends WebTestCase
{     
    public function testAdminDeparture()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/departure');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Vols de départs (11)');
    } 

    public function testAdminDepartureNew()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/departure/new');
        $this->assertSelectorTextContains('h1', 'Créer un vol de départ');
    } 

    public function testAdminDepartureEdit()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/departure/1/edit');
        $form = $crawler->selectButton('Sauvegarder')->form();
        $form['reference'] = 'LU9988';
        $client->submit($form);
        $departureRepository = static::$container->get(DepartureRepository::class);
        $this->assertStringContainsString('LU9988', $departureRepository->find(1)->getReference());
    }

    public function testAdminDepartureShow()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/departure/1');
        $this->assertSelectorTextContains('h1', 'Vol de départ (LU0000)');
        $this->assertEquals(
            1,
            $crawler->filter('tr:contains("LU0000")')->count());
    }
}