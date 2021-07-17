<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\ReservationFixtures;
use App\DataFixtures\Tests\UserFixtures;
use App\Repository\UserRepository;
use DateTime;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{ 
    public function testHomeUserOffline()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "LA MANIÈRE LA PLUS SÛRE");
        $this->assertCount(1, $crawler->filter('.search_bar'));
        $this->assertCount(1, $crawler->filter('.topbar'));
        $this->assertCount(1, $crawler->filter('.topbar_menu'));
        $this->assertSelectorTextContains('h2', "LES DESTINATIONS POPULAIRES");
        $this->assertLessThanOrEqual(4, $crawler->filter('article')->count());
        $this->assertSelectorTextContains('button', "RECHERCHE");
        $this->assertEquals(
            1,
            $crawler->filter('.menu_link:contains("CONNEXION")')->count());
    }

    public function testHomeUserOnline()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $this->assertEquals(
            1,
            $crawler->filter('.menu_link:contains("DECONNEXION")')->count());
        $this->assertEquals(
            1,
            $crawler->filter('.menu_link:contains("UnPrenom")')->count());
    }

    public function testHomeSearch()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);

        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('RECHERCHE')->form([
            "destination" => 1,
            "departure" => (new DateTime())->modify('+1 days')->format('d-m-Y'),
            "returned" => (new DateTime())->modify('+2 days')->format('d-m-Y'),
            "passagers" => 1
        ]);

        $client->submit($form);
        $this->assertSelectorTextContains('h1', "Choisissez vos vols de départ et de retour");
        $this->assertStringContainsString('LU0014', $client->getResponse()->getContent());
        $this->assertStringContainsString('LU0019', $client->getResponse()->getContent());
        $this->assertStringContainsString('LU0000', $client->getResponse()->getContent());
        $this->assertStringContainsString('LU0010', $client->getResponse()->getContent());
    }
}