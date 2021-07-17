<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminReservationTest extends WebTestCase
{     
    public function testAdminReservation()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/reservation');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Réservations (6)');
    } 

    public function testAdminReservationNew()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/reservation/new');
        $this->assertSelectorTextContains('h1', 'Créer une réservation');
    } 

    public function testAdminReservationEdit()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/reservation/1/edit');
        $form = $crawler->selectButton('Sauvegarder')->form();
        $form['reservation[reference]'] = 'LUXSPACEREFEDIT';
        $client->submit($form);
        $reservationRepository = static::$container->get(ReservationRepository::class);
        $this->assertStringContainsString('LUXSPACEREFEDIT', $reservationRepository->find(1)->getReference());
    }

    public function testAdminReservationShow()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/reservation/1');
        $this->assertSelectorTextContains('h1', 'Réservation (REF1)');
        $this->assertEquals(
            1,
            $crawler->filter('tr:contains("REF1")')->count());
    }
}