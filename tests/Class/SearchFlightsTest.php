<?php

namespace App\Tests\Class;

use App\Class\SearchFlight;
use App\Entity\Location;
use App\Tests\Entity\Error;
use App\Tests\Entity\Text;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SearchFlightsTest extends KernelTestCase
{    
    use Error;
    use Text;
    
    /**
     * @return SearchFlight
     */
    public function getEntity()
    {
        return (new SearchFlight)
                ->setDestination(new Location)
                ->setDeparture(new DateTime())
                ->setReturned((new DateTime())->modify('+2 day'))
                ->setPassagers(5);
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
        $search = $this->getEntity();
        $this->assertCount(0,$this->getErrors($search), implode(', ', $this->getErrors($search)));
    }

    public function testLocation()
    {
        $search = $this->getEntity();

        $location = new Location();
        $search->setDestination($location); 
        $this->assertThat($location,  $this->equalTo($search->getDestination()));
    }

    public function testDeparture()
    {
        $search = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            (new DateTime())->format('d-m-y'),
            $search->getDeparture()->format('d-m-y'));
        
        // Départ < maintenant
        $search->setDeparture((new DateTime())->modify('- 1 days '));
        $this->assertStringContainsString(
            "Date antérieure",
            implode(', ', $this->getErrors($search)));

         // Départ chaîne date
         $search->setDeparture("14-07-1988");
         $this->assertStringContainsString(
             "14-07-88",
             $search->getDeparture()->format('d-m-y'));
    }

    public function testReturn()
    {
        $search = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            (new DateTime())->modify('+2 days')->format('d-m-y'),
            $search->getReturned()->format('d-m-y'));
        
        // Retour < Départ
        $search->setReturned((new DateTime())->modify('- 3days '));
        $this->assertStringContainsString(
            "Date antérieure au départ",
            implode(', ', $this->getErrors($search)));
        
        // Départ chaîne date
        $search->setReturned("14-07-1988");
        $this->assertStringContainsString(
            "14-07-88",
            $search->getReturned()->format('d-m-y'));
    }

    public function testPassenger()
    {
        $search = $this->getEntity();

        // Obtenir
        $this->assertEquals(
            5,
            $search->getPassagers());
        
        // Min 1
        $search->setPassagers(0);
        $this->assertStringContainsString(
            "Invalide",
            implode(', ', $this->getErrors($search)));
        
        // Max 9
        $search->setPassagers(10);
        $this->assertStringContainsString(
            "Invalide",
            implode(', ', $this->getErrors($search)));
    }

    public function testTranformInDate()
    {
        $search = $this->getEntity();

        // Valide
        $date = $search->transformInDate("14-07-1988");
        $this->assertStringContainsString(
            "14-07-88",
            $date->format('d-m-y'));
        
        // Non valide
        $date = $search->transformInDate("ergherg");
        $this->assertStringContainsString(
            (new DateTime())->format('d-m-y'),
            $date->format('d-m-y'));
    }
}