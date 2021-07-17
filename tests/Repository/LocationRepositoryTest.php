<?php

namespace App\Repository;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\LocationFixtures;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LocationRepositoryTest extends WebTestCase
{
    private AbstractDatabaseTool $databaseTool;
    private EntityManagerInterface $em; 

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = self::$container;
        $this->databaseTool = $container->get(DatabaseToolCollection::class)->get();
        $this->em = $container->get('doctrine.orm.entity_manager');
    }
    public function testFindAll()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $location = $this->em->getRepository(Location::class)->findall();
        $this->assertCount(6, $location);
    }

    public function testFindWithNoThisLocation()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $location = $this->em->getRepository(Location::class)->findOneBy(['title' => 'Mars']);
        $locations = $this->em->getRepository(Location::class)->findWithNoThisLocation($location);
        $this->assertCount(5, $locations);
    }

    public function testFindPopularDestination()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $reservations = $this->em->getRepository(Location::class)->findPopularDestination();
        $this->assertCount(4, $reservations);
        $this->assertEquals('Mars', $reservations[0][0]->getTitle());
        $this->assertEquals('Lune', $reservations[1][0]->getTitle());
    } 

    public function testFindWithMinPrice()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $locations = $this->em->getRepository(Location::class)->findAllLocationWithDepartureMinPrice();
        $this->assertEquals(1000, $locations[0]['minPrice']);
    }
}