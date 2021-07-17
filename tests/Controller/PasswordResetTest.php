<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\ReservationFixtures;
use App\Repository\UserRepository;
use DateTime;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PasswordResetTest extends WebTestCase
{    
    public function testPasswordResetUserOffline()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mot-de-passe-oublie');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Réinitialiser mon mot de passe");
    }

    public function testPasswordResetUserOnline()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/mot-de-passe-oublie');
        $response = $client->getResponse();
        $this->assertEquals('/compte', $response->headers->get('location'));
    }

    public function testPasswordReset()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $crawler = $client->request('GET', '/mot-de-passe-oublie');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Réinitialiser')->form([
            "password_recovery_mail[email]" => 'toto@toto.toto'
        ]);

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertSelectorTextContains('.alert_danger', "Aucun compte trouvé pour l'adresse e-mail toto@toto.toto");
    }

    public function testPasswordResetToken()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $crawler = $client->request('GET', '/mot-de-passe-oublie');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Réinitialiser')->form([
            "password_recovery_mail[email]" => 'user1@user.be'
        ]);

        $client->submit($form);

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $this->assertNotNull($testUser->getPasswordReset()->getToken());
    }

    public function testPasswordResetSuccessChange()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $crawler = $client->request('GET', '/mot-de-passe-oublie');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Réinitialiser')->form([
            "password_recovery_mail[email]" => 'user1@user.be'
        ]);
        $client->submit($form);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $crawler = $client->request('GET', '/mot-de-passe-oublie/reinitialisation'.$testUser->getPasswordReset()->getToken());
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Sauvegarder')->form([
            "password_recovery[password][first]" => '%Password1',
            "password_recovery[password][second]" => '%Password1'
        ]);

        $client->submit($form);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $passwordEncoder = static::$container->get("security.password_encoder");

        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $this->assertSelectorTextContains('h1', 'Se connecter');
        $this->assertSelectorTextContains('.alert_success', 'Votre mot de passe a bien été réinitialisé');
        $this->assertTrue($passwordEncoder->isPasswordValid($testUser, '%Password1'));
    }

    public function testPasswordResetTokenInvalid()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $crawler = $client->request('GET', '/mot-de-passe-oublie');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Réinitialiser')->form([
            "password_recovery_mail[email]" => 'user1@user.be'
        ]);
        $client->submit($form);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $testUser->getPasswordReset()->setToken('zpoigjnzoierjhgiozeg');
        $crawler = $client->request('GET', '/mot-de-passe-oublie/reinitialisation'.$testUser->getPasswordReset()->getToken());
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('h1', "Réinitialiser mon mot de passe");
        $this->assertEquals(
            1,
            $crawler->filter('.alert_danger:contains("Token invalide ou expiré")')->count());
    }

    public function testPasswordResetEmailInvalid()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $crawler = $client->request('GET', '/mot-de-passe-oublie');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Réinitialiser')->form([
            "password_recovery_mail[email]" => 'user1user.be'
        ]);
        $client->submit($form);
        $userRepository = static::$container->get(UserRepository::class);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert_danger', 'Merci de renseigner une adresse e-mail valide');
    }

    public function testPasswordResetDateInvalid()
    {   
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $crawler = $client->request('GET', '/mot-de-passe-oublie/reinitialisation'.$testUser->getPasswordReset()->getToken());
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', "Réinitialiser mon mot de passe");
        $this->assertSelectorTextContains('.alert_danger', 'Token invalide ou expiré'); 
    }
}