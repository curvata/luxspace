<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\Client;

class ReservationTest extends WebTestCase
{    
    public function testReservation()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $crawler = $client->request('GET', '/reservation?passengers=2&departure=1&return=2');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Réservation pour Mars');
        $this->assertEquals(1, $crawler->filter('tr:contains("LU0000")')->count());
        $this->assertEquals(1, $crawler->filter('tr:contains("LU0014")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Vol aller")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Vol retour")')->count());
        $this->assertEquals(1, $crawler->filter('h3:contains("Passager numéro 1")')->count());
        $this->assertEquals(1, $crawler->filter('h3:contains("Passager numéro 2")')->count());
        $this->assertEquals(1, $crawler->filter('button:contains("Confirmer ma réservation")')->count());

        $this->assertEquals(0, $crawler->filter('h3:contains("Passager numéro 3")')->count());
    }

    public function testRecapReservation()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('reservation');
        $crawler = $client->request('POST', '/reservation/recapitulatif',[
            'passengers' => [
                1 => [
                    "firstname" => "Mike",
                    "lastname" => "Menezes"
                ],
                2 => [
                    "firstname" => "Bamboo",
                    "lastname" => "Cancelinha"
                ]
            ],
            'departure' => 2,
            'return' => 2,
            'token' => $csrfToken
            ]);
        $this->assertResponseIsSuccessful();
      
        $this->assertSelectorTextContains('h1', 'Récapitulatif de votre réservation pour Mars');
        $this->assertEquals(1, $crawler->filter('tr:contains("LU0010")')->count());
        $this->assertEquals(1, $crawler->filter('tr:contains("LU0014")')->count());
        $this->assertEquals(4, $crawler->filter('td:contains("1,000.00")')->count());
        $this->assertEquals(1, $crawler->filter('span:contains("4,000.00")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Menezes")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Cancelinha")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Vol aller")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Vol retour")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Détails")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Payer")')->count());
    }

    public function testRecapReservationPassengerInvalid()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('reservation');
        $crawler = $client->request('POST', '/reservation/recapitulatif',[
            'passengers' => [
                1 => [
                    "firstname" => "M",
                    "lastname" => "M"
                ]
            ],
            'departure' => 2,
            'return' => 2,
            'token' => $csrfToken
            ], [], ['HTTP_REFERER' => '/reservation?passengers=1&departure=2&return=2']);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.alert_danger', 'Merci de renseigner des noms et prénoms valides');
    }

    public function testReservationSuccess()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $client->request('GET', '/reservation/merci/STRIPEREF1');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('p', 'user1@user.be');
        $this->assertSelectorTextContains('p', 'Nous vous remercions pour votre commande n° REF1 ');
        $this->assertSelectorTextContains('p', 'UnPrenom, ');
    }

    public function testReservationCancel()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $client->request('GET', '/reservation/erreur/STRIPEREF1');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('p', 'Il semblerait que votre paiement a échoué pour votre réservation REF1');
        $this->assertSelectorTextContains('p', 'UnPrenom, ');
    }
}