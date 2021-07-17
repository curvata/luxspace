<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\LocationFixtures;
use App\DataFixtures\Tests\OneLocationFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DestinationTest extends WebTestCase
{    
    public function testDesination()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/destination');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "NOS DESTINATIONS");
    }

    public function testDesinationShow()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $crawler = $client->request('GET', '/destination/1');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Mars");
        $this->assertSelectorTextContains('h2', "VOUS AIMEREZ");
        $this->assertEquals(
            4,
            $crawler->filter('h3')->count());
    }

    public function testDesinationShowWithoutAnotherLocation()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([OneLocationFixtures::class]);
        $crawler = $client->request('GET', '/destination/1');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Mars");
        $this->assertEquals(
            0,
            $crawler->filter('h2:contains("VOUS AIMEREZ")')->count());
        $this->assertEquals(
            0,
            $crawler->filter('h3')->count());
    }
}