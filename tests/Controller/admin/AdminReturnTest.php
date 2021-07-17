<?php

namespace App\Tests\Controller;

use App\DataFixtures\Tests\AppFixtures;
use App\Repository\ReturnedRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminReturnTest extends WebTestCase
{     
    public function testAdminReturn()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/returned');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Vols de retours (11)');
    } 

    public function testAdminReturnNew()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/returned/new');
        $this->assertSelectorTextContains('h1', 'Créer un vol de retour');
    } 

    public function testAdminReturnEdit()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/returned/1/edit');
        $form = $crawler->selectButton('Sauvegarder')->form();
        $form['reference'] = 'LU4545';
        $client->submit($form);
        $returnRepository = static::$container->get(ReturnedRepository::class);
        $this->assertStringContainsString('LU4545', $returnRepository->find(1)->getReference());
    }

    public function testAdminDepartureShow()
    {
        $client = static::createClient();
        self::$container->get(DatabaseToolCollection::class)->get()->loadFixtures([AppFixtures::class]);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('user1@user.be');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/returned/1');
        $this->assertSelectorTextContains('h1', 'Vol de retour (LU0019)');
        $this->assertEquals(
            1,
            $crawler->filter('tr:contains("LU0019")')->count());
    }
}