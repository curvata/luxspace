<?php

namespace App\Tests\Entity;

use App\Entity\Departure;
use App\Entity\Location;
use App\Entity\Reservation;
use App\Entity\Returned;
use App\Entity\User;
use DateTime;
use Exception;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReturnTest extends KernelTestCase
{    
    use Error;
    use Text;
    
    /**
     * @return Returned
     */
    public function getEntity(): Returned
    {
        return (new Returned)
                ->setReference('LU9988')
                ->setDate((new DateTime())->modify('+1 days'))
                ->setSeat(1000)
                ->setRocket('FALCON9')
                ->setDuration(25)
                ->setPrice(5000)
                ->setFfrom((new Location)->setTitle('Thailande'));
    }
    
    /**
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        /** @var ValidatorInterface */
        return self::$container->get('debug.validator');
    }

    public function testFfrom()
    {
        $return = $this->getEntity();

        $this->assertStringContainsString(
            "Thailande",
            $return->getFfrom()->getTitle());
    }

    public function testValid()
    {
         $return = $this->getEntity();
         $this->assertCount(0,$this->getErrors($return), implode(', ', $this->getErrors($return)));
    }

    public function testReservation()
    {
        $return = $this->getEntity();
        $reservation = (new Reservation())->setReference('BROL');
        $return->addReservation($reservation);
        $this->assertTrue($return->getReservations()->contains($reservation));
    }

    public function testToString()
    {
        $return = $this->getEntity();

        $this->assertStringContainsString(
            "LU9988 Thailande ". (new DateTime())->modify('+1 days')->format('Y-m-d H-m-s'),
            $return);
    }

    public function testReference()
    {
        $return = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
          "LU9988",
          $return->getReference());

        // Non vide
        $return = $this->getEntity();
        $return->setReference('');
        $this->assertStringContainsString(
            "Merci de renseigner la référence du vol",
            implode(', ', $this->getErrors($return)));
        
        // Invalide
        $return = $this->getEntity();
        $return->setReference('L000');
        $this->assertStringContainsString(
            "La référence doit contenir 2 lettres suivies de 4 chiffres",
            implode(', ', $this->getErrors($return)));
    }

    public function testDate()
    {
        $return = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            (new DateTime())->modify('+1 days')->format('d-m-y'),
            $return->getDate()->format('d-m-y'));
        
        // Min J+1
        $return = $this->getEntity();
        $return->setDate(new DateTime());
        $this->assertStringContainsString(
            "Merci de renseigner une date valide",
            implode(', ', $this->getErrors($return)));
    }

    public function testSeat()
    {
        $return = $this->getEntity();

        // Obtenir
        $this->assertEquals(
            1000,
            $return->getSeat());

        // Max 1000
        $return = $this->getEntity();
        $return->setSeat(1001);
        $this->assertStringContainsString(
            "Vous ne pouvez pas renseigner plus de 1000 places",
            implode(', ', $this->getErrors($return)));
    }

    public function testRocket()
    {
        $return = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "FALCON9",
            $return->getRocket());
        
        // Min 5
        $return = $this->getEntity();
        $return->setRocket("1");
        $this->assertStringContainsString(
            "Le nom de la fusée doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($return)));

         // Max 30
         $return = $this->getEntity();
         $return->setRocket($this->getText(31));
         $this->assertStringContainsString(
             "Le nom de la fusée ne peut pas comporter plus de 30 caractères",
             implode(', ', $this->getErrors($return)));
    }

    public function testDuration()
    {
        $return = $this->getEntity();

        // Obtenir
        $this->assertEquals(
            25,
            $return->getDuration());
        
        // Max 25
        $return = $this->getEntity();
        $return->setDuration(51);
        $this->assertStringContainsString(
            "La durée du vol ne peut dépasser 25 heures",
            implode(', ', $this->getErrors($return)));
    }

    public function testPrice()
    {
        $return = $this->getEntity();

        // Obtenir
        $this->assertEquals(
            5000,
            $return->getPrice());
        
        // Max 10000
        $return = $this->getEntity();
        $return->setPrice(10001);
        $this->assertStringContainsString(
            "Le prix doit être inférieur à 10000 €",
            implode(', ', $this->getErrors($return)));
    }
}