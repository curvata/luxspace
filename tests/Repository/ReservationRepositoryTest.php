<?php

namespace App\Repository;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\ReservationFixtures;
use App\Entity\Reservation;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationRepositoryTest extends WebTestCase
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
        $reservations = $this->em->getRepository(Reservation::class)->findall();
        $this->assertCount(6, $reservations);
    } 

    public function testFindReservationPaidAndRefund()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'user1@user.be']);
        $reservations = $this->em->getRepository(Reservation::class)->findReservationPaidAndRefund($user);
        $this->assertCount(2, $reservations);
    }

    public function testFindShortDepartureDate()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'user1@user.be']);
        $reservation = $this->em->getRepository(Reservation::class)->findShortDepartureDate($user);
        $this->assertStringContainsString('Mars', $reservation[0]->getDeparture()->getDestination()->getTitle());
        $this->assertStringContainsString((new DateTime())->modify('+1 days')->format('d-m-y'), $reservation['date']->format('d-m-y'));
    }

    public function testFindReservationByMonthYearNow()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $reservations = $this->em->getRepository(Reservation::class)->findReservationByMonthYearNow();
        $this->assertEquals(5, $reservations[0]['reservations']);
    }
}