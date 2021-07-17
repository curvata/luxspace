<?php

namespace App\Repository;

use App\Class\SearchFlight;
use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\ReturnedFixtures;
use App\Entity\Location;
use App\Entity\Returned;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReturnedRepositoryTest extends WebTestCase
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
        $return = $this->em->getRepository(Returned::class)->findall();
        $this->assertCount(11, $return);
    } 

    public function testSearchFlights()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $location = $this->em->getRepository(Location::class)->findOneBy(['title' => 'Mars']);

        // Available flights
        $search = (new SearchFlight())->setDestination($location)->setPassagers(5);
        $search->setReturned((new DateTime())->modify('+2 days'));
        $returns = $this->em->getRepository(Returned::class)->findFlights($search);

        $this->assertCount(2, $returns);

        // Flights unavailable
        $search->setReturned((new DateTime())->modify('+3 days'));
        $returns = $this->em->getRepository(Returned::class)->findFlights($search);

        $this->assertCount(0, $returns);
    }

    public function testFindValidDateFlight()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $location = $this->em->getRepository(Location::class)->findOneBy(['title' => 'Mars']);
        $returns = $this->em->getRepository(Returned::class)->findValidDateFlight($location);

        $this->assertCount(2, $returns);
    }
}