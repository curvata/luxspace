<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\DepartureFixtures;
use App\DataFixtures\Tests\ReturnedFixtures;
use DateTime;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListDaysNoFlightsTest extends WebTestCase
{    
    public function testListDaysNoFlightsWithValidDestination()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $client->request('GET', '/list-days-no-flights/1');
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayNotHasKey((new DateTime())->modify('+1 days')->format('ymd'), $data['departures']);
        $this->assertArrayHasKey((new DateTime())->modify('+2 days')->format('ymd'), $data['departures']);
        $this->assertArrayHasKey((new DateTime())->modify('+1 days')->format('ymd'), $data['returneds']);
        $this->assertArrayNotHasKey((new DateTime())->modify('+2 days')->format('ymd'), $data['returneds']);
    }

    public function testListDaysNoFlightsWithInvalidDestination()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $client->request('GET', '/list-days-no-flights/10');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString("La destination n\'existe pas", $data);
    }
}