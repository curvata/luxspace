<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Repository\DepartureRepository;
use App\Repository\LocationRepository;
use App\Repository\UserRepository;
use DateTime;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminGraphTest extends WebTestCase
{     
    public function testAdminDeparture()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $client->request('GET', '/admin/graph');
        $data = json_decode($client->getResponse()->getContent(), true);
        $month = (new DateTime())->format('n');
        $this->assertEquals(5, $data['reservation'][$month]);
        $this->assertEquals(2, $data['user'][$month]);
    } 
}