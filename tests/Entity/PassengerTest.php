<?php

namespace App\Tests\Entity;

use App\Entity\Passenger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PassengerTest extends KernelTestCase
{
    use Error;
    use Text;

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
     * @return Passenger
     */
    public function getEntity(): Passenger
    {
        return (new Passenger)
                ->setLastname('Cancelinha Menezes')
                ->setFirstname('Mike');
    }

    public function testValid()
    {
        $passenger = $this->getEntity();
        $this->assertCount(0,$this->getErrors($passenger), implode(', ', $this->getErrors($passenger)));
    }

    public function testGetStringName()
    {
        $passenger = $this->getEntity();

        $this->assertStringContainsString(
            "Cancelinha Menezes Mike",
            $passenger);
    }

    public function testLastname()
    {
        $passenger = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "Cancelinha Menezes",
            $passenger->getLastname());

        // Non vide
        $passenger = $this->getEntity();
        $passenger->setLastname('');
        $this->assertStringContainsString(
            "Le nom doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($passenger)));
   
        // Sans chiffre
        $passenger = $this->getEntity();
        $passenger->setLastname('1Mike');
        $this->assertStringContainsString(
            "Le nom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($passenger)));

        // Sans symbole
        $passenger = $this->getEntity();
        $passenger->setLastname('%Mike');
        $this->assertStringContainsString(
            "Le nom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($passenger)));
        
        // Min 5
        $passenger = $this->getEntity();
        $passenger->setLastname('Mene');
        $this->assertStringContainsString(
            "Le nom doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($passenger)));

         // Min 30
         $passenger = $this->getEntity();
         $passenger->setLastname($this->getText(31));
         $this->assertStringContainsString(
             "Le nom ne peut pas comporter plus de 30 caractères",
             implode(', ', $this->getErrors($passenger)));
    }

    public function testFirstname()
    {
        $passenger = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "Mike",
            $passenger->getFirstname());

        // Non vide
        $passenger = $this->getEntity();
        $passenger->setFirstname('');
        $this->assertStringContainsString(
            "Le prénom doit comporter au moins 3 caractères",
            implode(', ', $this->getErrors($passenger)));
   
        // Sans chiffre
        $passenger = $this->getEntity();
        $passenger->setFirstname('1Mike');
        $this->assertStringContainsString(
            "Le prénom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($passenger)));

        // Sans symbole
        $passenger = $this->getEntity();
        $passenger->setFirstname('%Mike');
        $this->assertStringContainsString(
            "Le prénom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($passenger)));
        
        // Min 3
        $passenger = $this->getEntity();
        $passenger->setFirstname('Mi');
        $this->assertStringContainsString(
            "Le prénom doit comporter au moins 3 caractères",
            implode(', ', $this->getErrors($passenger)));

         // Max 20
         $passenger = $this->getEntity();
         $passenger->setFirstname($this->getText(21));
         $this->assertStringContainsString(
             "Le prénom ne peut pas comporter plus de 20 caractères",
             implode(', ', $this->getErrors($passenger)));
    }
}