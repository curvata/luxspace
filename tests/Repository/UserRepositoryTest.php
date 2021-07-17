<?php

namespace App\Repository;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\UserFixtures;
use App\Entity\Location;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepositoryTest extends WebTestCase
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
        $users = $this->em->getRepository(User::class)->findall();
        $this->assertCount(3, $users);
    }

    public function testUpgradePassword()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $repoUser = $this->em->getRepository(User::class);
        $user = $repoUser->findOneBy(['email' => 'user1@user.be']);
        $repoUser->upgradePassword($user, 'password');
        $user = $repoUser->findOneBy(['email' => 'user1@user.be']);
        $this->assertStringContainsString('password', $user->getPassword());
    }

    public function testFindUserByMonthYearNow()
    {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $users = $this->em->getRepository(User::class)->findUserByMonthYearNow();
        $this->assertEquals(2, $users[0]['users']);
    }
}