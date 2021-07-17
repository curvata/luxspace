<?php

namespace App\Tests\Entity;

use App\Entity\Departure;
use App\Entity\Location;
use App\Entity\Picture;
use App\Entity\Returned;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;

class LocationTest extends KernelTestCase
{    
    use Error;
    use Text;
    
    /**
     * @return Location
     */
    public function getEntity(): Location
    {
        $file = new File(__DIR__.'/image.jpg');
        return (new Location)
                ->setTitle('Mars')
                ->setShortDescription('Petite description de la planète Mars')
                ->setDescription('Grosse description de la planète Mars, Grosse description de la planète Mars')
                ->setPictureFiles([$file])
                ->setCreatedAt(new DateTime())
                ->setHeaderPictureFile($file)
                ->setPictureHeader("BLABLA.JPEG")
                ->setSlug('SLUG');
    }
    
    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        self::bootKernel();
        /** @var ValidatorInterface */
        return self::$container->get('debug.validator');
    }

    public function testValid()
    {
        $location = $this->getEntity();
        $this->assertCount(0,$this->getErrors($location), implode(', ', $this->getErrors($location)));
    }

    public function testDeparture()
    {
        $location = $this->getEntity();
        $departure = new Departure();
        $location->addDeparture($departure);
        $this->assertTrue($location->getDepartures()->contains($departure));
    }

    public function testToString()
    {
        $location = $this->getEntity();

        $this->assertStringContainsString(
           "Mars",
           $location);
    }

    public function testCreatedAt()
    {
        $location = $this->getEntity();
        $location->setCreatedAt(new DateTime('14-07-1988'));
        $this->assertStringContainsString(
            "14-07-1988",
            $location->getCreatedAt()->format(('d-m-Y')));
    }

    public function testSlug()
    {
        $location = $this->getEntity();

        // Transform avec SLUGIFY
        $location->setSlug('blabla blabla slug');
        $this->assertStringContainsString(
            "blabla-blabla-slug",
            $location->getSlug());

        // Non vide
        $location->setSlug('');
        $this->assertStringContainsString(
            "Merci de renseigner le slug",
            implode(', ', $this->getErrors($location)));
    }

    public function testGetUpdateDate()
    {
        $location = $this->getEntity();

        $location->setUpdatedAt(new DateTime('14-07-1988'));
        $this->assertStringContainsString(
            "14-07-1988",
            $location->getUpdatedAt()->format(('d-m-Y')));
    }

    public function testPictures()
    {
        $location = $this->getEntity();

        // Format invalide
        $file = new File(__DIR__.'/image2.svg');
        $location->setPictureFiles([$file]);
     
        // obtenir
        $this->assertCount(2, $location->getPictures());
    }

    public function testHeaderPicture()
    {
        $location = $this->getEntity();

        // Format invalide
        $file = new File(__DIR__.'/image2.svg');
        $location->setHeaderPictureFile($file);
        $this->assertStringContainsString(
            "Uniquement les formats jpeg et png sont acceptés",
            implode(', ', $this->getErrors($location)));

        // Obtenir
        $this->assertThat($file,  $this->equalTo($location->getHeaderPictureFile()));

        $this->assertStringContainsString(
            "BLABLA.JPEG",
            $location->getPictureHeader());
    }

    public function testTitle()
    {
        $location = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
           "Mars",
           $location->getTitle());

        // Non vide
        $location = $this->getEntity();
        $location->setTitle('');
        $this->assertStringContainsString(
            "Le titre doit comporter au moins 4 caractères",
            implode(', ', $this->getErrors($location)));
        
        // Min 4
        $location = $this->getEntity();
        $location->setTitle($this->getText(3));
        $this->assertStringContainsString(
            "Le titre doit comporter au moins 4 caractères",
            implode(', ', $this->getErrors($location)));
        
        // Max 30
        $location = $this->getEntity();
        $location->setTitle($this->getText(31));
        $this->assertStringContainsString(
            "Le titre ne peut pas comporter plus de 30 caractères",
            implode(', ', $this->getErrors($location)));
    }

    public function testShortDescription()
    {
        $location = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
          "Petite description de la planète Mars",
          $location->getShortDescription());

        // Non vide
        $location = $this->getEntity();
        $location->setShortDescription('');
        $this->assertStringContainsString(
            "La description courte doit comporter au moins 20 caractères",
            implode(', ', $this->getErrors($location)));
        
        // Min 20
        $location = $this->getEntity();
        $location->setShortDescription($this->getText(19));
        $this->assertStringContainsString(
            "La description courte doit comporter au moins 20 caractères",
            implode(', ', $this->getErrors($location)));
        
        // Max 190
        $location = $this->getEntity();
        $location->setShortDescription($this->getText(201));
        $this->assertStringContainsString(
            "La description courte ne peut pas comporter plus de 190 caractères",
            implode(', ', $this->getErrors($location)));
    }

    public function testDescription()
    {
        $location = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
          "Grosse description de la planète Mars, Grosse description de la planète Mars",
          $location->getDescription());

        // Non vide
        $location = $this->getEntity();
        $location->setDescription('');
        $this->assertStringContainsString(
            "La description doit comporter au moins 50 caractères",
            implode(', ', $this->getErrors($location)));
        
        // Min 50
        $location = $this->getEntity();
        $location->setDescription($this->getText(49));
        $this->assertStringContainsString(
            "La description doit comporter au moins 50 caractères",
            implode(', ', $this->getErrors($location)));
        
        // Max 950
        $location = $this->getEntity();
        $location->setDescription($this->getText(951));
        $this->assertStringContainsString(
            "La description ne peut pas comporter plus de 950 caractères",
            implode(', ', $this->getErrors($location)));
    }
}