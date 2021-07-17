<?php

namespace App\DataFixtures\Tests;

use App\Entity\Location;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OneLocationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $location = (new Location)
            ->setTitle('Mars')
            ->setShortDescription('Petite description de la planète Mars')
            ->setDescription('Grosse description de la planète Mars, Grosse description de la planète Mars')
            ->setCreatedAt(new DateTime())
            ->setPictureHeader("BLABLA.JPEG")
            ->setSlug('SLUG');
        
        $manager->persist($location);
        $manager->flush();
    }
}
