<?php

namespace App\Tests\Entity;

use App\Entity\Location;
use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PictureTest extends KernelTestCase
{
    use Error;

     /**
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        /** @var ValidatorInterface */
        return self::$container->get('debug.validator');
    }

    /**
     * @return Picture
     */
    public function getEntity(): Picture
    {
        $file = new File(__DIR__.'/image.jpg');
        return (new Picture)
                ->setFilename('picture.jpeg')
                ->setPictureFile($file)
                ->setLocation(new Location);
    }

    public function testValid()
    {
         $picture = $this->getEntity();
         $this->assertStringContainsString('small_picture.jpeg', $picture->getSmallPicture());
    }

    public function testSmallPicture()
    {
        $picture = $this->getEntity();
        $this->assertStringContainsString(
          "picture.jpeg",
          $picture->getFilename());
    }

    public function testToString()
    {
        $picture = $this->getEntity();
        $this->assertStringContainsString('picture.jpeg', $picture);
    }

    public function testPicture()
    {
        $file = new File(__DIR__.'/image.jpg');
        $picture = $this->getEntity();
        $picture->setPictureFile($file);
        $this->assertThat($file, $this->equalTo($picture->getPictureFile()));
    }

    public function testLocation()
    {
        $picture = $this->getEntity();
        $picture->setLocation((new Location())->setTitle('bamboo'));
        $this->assertStringContainsString('bamboo', $picture->getLocation()->getTitle());
    }
}