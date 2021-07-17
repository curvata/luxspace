<?php

namespace App\Tests\Entity;

use App\Entity\Departure;
use App\Entity\Location;
use App\Entity\Reservation;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DepartureTest extends KernelTestCase
{    
    use Error;
    use Text;
    
    /**
     * @return Departure
     */
    public function getEntity(): Departure
    {
        return (new Departure)
                ->setReference('LU9999')
                ->setDate((new DateTime())->modify('+1 days'))
                ->setSeat(1000)
                ->setRocket('FALCON9')
                ->setDuration(25)
                ->setPrice(5000)
                ->setDestination((new Location)->setTitle('Thailande'));
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
        $departure = $this->getEntity();
        $this->assertCount(0,$this->getErrors($departure), implode(', ', $this->getErrors($departure)));
    }

    public function testToString()
    {
        $departure = $this->getEntity();

        $this->assertStringContainsString(
            "LU9999 Thailande ". (new DateTime())->modify('+1 days')->format('Y-m-d H-m-s'),
            $departure);
    }

    public function testDestination()
    {
        $return = $this->getEntity();

        $this->assertStringContainsString(
            "Thailande",
            $return->getDestination()->getTitle());
    }

    public function testReservation()
    {
        $departure = $this->getEntity();
        $reservation = (new Reservation())->setReference('BROL');
        $departure->addReservation($reservation);
        $this->assertTrue($departure->getReservations()->contains($reservation));
    }

    public function testReference()
    {
        $departure = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "LU9999",
            $departure->getReference());

        // Non vide
        $departure = $this->getEntity();
        $departure->setReference('');
        $this->assertStringContainsString(
            "Merci de renseigner la référence du vol",
            implode(', ', $this->getErrors($departure)));
        
        // Invalide
        $departure = $this->getEntity();
        $departure->setReference('L000');
        $this->assertStringContainsString(
            "La référence doit contenir 2 lettres suivies de 4 chiffres",
            implode(', ', $this->getErrors($departure)));
    }

    public function testDate()
    {
        $departure = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            (new DateTime())->modify('+1 days')->format('d-m-y'),
            $departure->getDate()->format('d-m-y'));
        
        // Min J+1
        $departure = $this->getEntity();
        $departure->setDate(new DateTime());
        $this->assertStringContainsString(
            "Merci de renseigner une date valide",
            implode(', ', $this->getErrors($departure)));
    }

    public function testSeat()
    {
        $departure = $this->getEntity();

        // Obtenir
        $this->assertEquals(
            1000,
            $departure->getSeat());

        // Min 50
        $departure = $this->getEntity();
        $departure->setSeat(49);
        $this->assertStringContainsString(
            "Vous ne pouvez pas renseigner moins de 50 places",
            implode(', ', $this->getErrors($departure)));

        // Max 1000
        $departure = $this->getEntity();
        $departure->setSeat(1001);
        $this->assertStringContainsString(
            "Vous ne pouvez pas renseigner plus de 1000 places",
            implode(', ', $this->getErrors($departure)));
    }

    public function testRocket()
    {
        $departure = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "FALCON9",
            $departure->getRocket());
        
        // Min 5
        $departure = $this->getEntity();
        $departure->setRocket("1");
        $this->assertStringContainsString(
            "Le nom de la fusée doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($departure)));

         // Max 30
         $departure = $this->getEntity();
         $departure->setRocket($this->getText(31));
         $this->assertStringContainsString(
             "Le nom de la fusée ne peut pas comporter plus de 30 caractères",
             implode(', ', $this->getErrors($departure)));
    }

    public function testDuration()
    {
        $departure = $this->getEntity();

        // Obtenir
        $this->assertEquals(
            25,
            $departure->getDuration());
        
        // Max 25
        $departure = $this->getEntity();
        $departure->setDuration(26);
        $this->assertStringContainsString(
            "La durée du vol ne peut dépasser 25 heures",
            implode(', ', $this->getErrors($departure)));
    }

    public function testPrice()
    {
        $departure = $this->getEntity();

        // Obtenir
        $this->assertEquals(
            5000,
            $departure->getPrice());
        
        // Max 10000
        $departure = $this->getEntity();
        $departure->setPrice(10001);
        $this->assertStringContainsString(
            "Le prix doit être inférieur à 10000 €",
            implode(', ', $this->getErrors($departure)));
    }
}