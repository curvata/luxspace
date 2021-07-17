<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Repository\PassengerRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountTest extends WebTestCase
{     
    public function testAccountUserOffline()
    {
        $client = static::createClient();
        $client->request('GET', '/compte');
        $response = $client->getResponse();
        $this->assertEquals('/connexion', $response->headers->get('location'));
    } 

    public function testAccountUserOnline()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte');

        $this->assertSelectorTextContains('h1', "Tableau de bord");
        $this->assertSelectorTextContains('h2', "Bonjour UnPrenom,");
        $this->assertEquals(
            1,
            $crawler->filter('a:contains("Gérer mes cordonnées")')->count());
        $this->assertEquals(
            1,
            $crawler->filter('a:contains("Modifier mon mot de passe")')->count());
        $this->assertEquals(
            1,
            $crawler->filter('a:contains("Gérer mes réservations")')->count());
        $this->assertEquals(
            1,
            $crawler->filter('p:contains("Mars")')->count());
    } 

    public function testAccountReservation()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mes-reservations');
        $this->assertSelectorTextContains('h1', "Mes réservations (2)");
    }

    public function testAccountInvoice()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mes-reservations/1');
        $this->assertSelectorTextContains('h1', "Réservation");
        $this->assertEquals(
            1,
            $crawler->filter('strong:contains("REF1")')->count());
    }

    public function testAccountIncoiceNotYourReservation()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mes-reservations/5');
        $this->assertResponseRedirects();
        $response = $client->getResponse();
        $this->assertEquals('/compte/mes-reservations', $response->headers->get('location'));
    }

    public function testAccountIncoiceNotExist()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mes-reservations/50');
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testAccountPassenger()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mes-reservations/passager/1');
        $this->assertSelectorTextContains('h1', "Les passagers");
        $this->assertSelectorTextContains('h4', "Passager numéro 1");
        $this->assertEquals(
            1,
            $crawler->filter('input[value="UnPrenomPassager"]')->count());
        $this->assertEquals(
            1,
            $crawler->filter('input[value="UnNomPassager"]')->count());
    }

    public function testAccountPassengerEdit()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $passager = static::$container->get(PassengerRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mes-reservations/passager/1');
        $form = $crawler->selectButton('Sauvegarder')->form();
        $form['passengers[1][firstname]'] = "Bamboo";
        $form['passengers[1][lastname]'] = "Menezes";
        $client->submit($form);
        $this->assertStringContainsString('Bamboo', $passager->find(1)->getFirstname());
        $this->assertStringContainsString('Menezes', $passager->find(1)->getLastname()); 
    }

    public function testAccountPassengerUserNotValid()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mes-reservations/passager/5');
        $this->assertResponseRedirects();
        $response = $client->getResponse();
        $this->assertEquals('/compte/mes-reservations', $response->headers->get('location'));
    }

    public function testAccountPassengerReservationNotExist()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mes-reservations/passager/70');
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testAccountProfil()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mon-profil');
        $this->assertEquals(
            1,
            $crawler->filter('td:contains("user1@user.be")')->count());
        $this->assertEquals(
            1,
            $crawler->filter('td:contains("UnNom")')->count());
        $this->assertEquals(
            1,
            $crawler->filter('td:contains("UnPrenom")')->count());
    }

    public function testAccountEditProfil()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mon-profil/'.$testUser->getId());
        $this->assertEquals(
            8,
            $crawler->filter('input')->count());
        $this->assertEquals(
            1,
            $crawler->filter('input[value="Rue de Luxembourg"]')->count());

        $form = $crawler->selectButton('Sauvegarder')->form();
        $form['address'] = "Rue des Belges";
        $client->submit($form);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $this->assertStringContainsString("Rue des Belges", $testUser->getAddress());
    }

    public function testAccountEditPassword()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $passwordEncoder = static::$container->get("security.password_encoder");


        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/compte/mon-profil/mot-de-passe');

        $this->assertEquals(
            3,
            $crawler->filter('input')->count());

        $form = $crawler->selectButton('Sauvegarder')->form([
            "password_recovery[password][first]" => "%LapetiteVoiture9",
            "password_recovery[password][second]" => "%LapetiteVoiture9"
        ]);
        $client->submit($form);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $this->assertTrue($passwordEncoder->isPasswordValid($testUser, '%LapetiteVoiture9'));
    }
}