<?php

namespace App\Repository;

use App\DataFixtures\Tests\AppFixtures;
use App\DataFixtures\Tests\PasswordResetFixtures;
use App\Entity\PasswordReset;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PasswordResetRepositoryTest extends WebTestCase
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
        $passwordResets = $this->em->getRepository(PasswordReset::class)->findall();
        $this->assertCount(1, $passwordResets);
    }
}