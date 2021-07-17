<?php

namespace App\Tests\Entity;

use App\DataFixtures\Prod\ProdFixtures;
use App\Repository\DepartureRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Repository\ReturnedRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProdFixturesTest extends WebTestCase
{
    public function testProdFixtures()
    {   
        self::bootKernel();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([ProdFixtures::class]);
        $users = (static::$container->get(UserRepository::class))->findAll();
        $reservations = (static::$container->get(ReservationRepository::class))->findAll();
        $this->assertCount(1, $users);
        $this->assertCount(4, $reservations);
    } 
}