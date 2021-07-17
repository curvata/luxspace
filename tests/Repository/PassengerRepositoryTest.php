<?php

namespace App\Repository;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\PassengerFixtures;
use App\Entity\Passenger;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PassengerRepositoryTest extends WebTestCase
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
        $passengers = $this->em->getRepository(Passenger::class)->findall();
        $this->assertCount(1, $passengers);
    }
}