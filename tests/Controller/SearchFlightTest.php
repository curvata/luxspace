<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\DepartureFixtures;
use App\DataFixtures\Tests\ReturnedFixtures;
use DateTime;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchFlightTest extends WebTestCase
{    
    public function testSearchFlightWithoutSearchData()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/recherche-vols');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Choisissez vos vols de départ et de retour");
        $this->assertEquals(
            2,
            $crawler->filter('p:contains("Aucun vol de retour disponible, veuillez choisir une autre date.")')->count());
    }

    public function testSearchFlightWithValidSearchData()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);

        $crawler = $client->request('GET', '/recherche-vols', [
            'destination' => 1,
            'departure' => (new DateTime())->modify('+1 days')->format('d-m-Y'),
            'returned' => (new DateTime())->modify('+2 days')->format('d-m-Y'),
            'passagers' => 1
            ]);

            $this->assertSelectorTextContains('h1', "Choisissez vos vols de départ et de retour");
            $this->assertEquals(
            6,
            $crawler->filter('tr')->count());
            $this->assertEquals(
                1,
                $crawler->filter('tr:contains("LU0000")')->count());
            $this->assertEquals(
                1,
                $crawler->filter('tr:contains("LU0010")')->count());
            $this->assertEquals(
                1,
                $crawler->filter('tr:contains("LU0014")')->count());
            $this->assertEquals(
                1,
                $crawler->filter('tr:contains("LU0019")')->count());
        
    }

    public function testSearchFlightNotvalid()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);

        $crawler = $client->request('GET', '/recherche-vols', [
            'destination' => 20,
            'departure' => (new DateTime())->modify('+5 days')->format('d-m-Y'),
            'returned' => (new DateTime())->modify('+1 days')->format('d-m-Y'),
            'passagers' => 10
            ]);

        $this->assertEquals(
            2,
            $crawler->filter('.form-error-message:contains("Cette valeur n\'est pas valide.")')->count());
        $this->assertEquals(
            1,
            $crawler->filter('.form-error-message:contains("Date antérieure au départ")')->count());
    }
}