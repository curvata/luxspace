<?php

namespace App\Tests\Class;

use App\Class\Contact;
use App\Tests\Entity\Error;
use App\Tests\Entity\Text;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactTest extends KernelTestCase
{    
    use Error;
    use Text;
    
    /**
     * @return Contact
     */
    public function getEntity()
    {
        return (new Contact)
                ->setFirstname('Mike')
                ->setLastname('Un nom de famille')
                ->setEmail('test@test.be')
                ->setMessage('Ceci est un petit message');
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
        $contact = $this->getEntity();
        $this->assertCount(0,$this->getErrors($contact), implode(', ', $this->getErrors($contact)));
    }

    public function testEmail()
    {
        $contact = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "test@test.be",
            $contact->getEmail());

        // Invalide
        $contact = $this->getEntity();
        $contact->setEmail('blablamail.be');
        $this->assertStringContainsString(
            "L'e-mail \"blablamail.be\" n'est pas valide",
            implode(', ', $this->getErrors($contact)));
        
        // Non vide
        $contact = $this->getEntity();
        $contact->setEmail('');
        $this->assertStringContainsString(
            "L'e-mail doit comporter au moins 6 caractères",
            implode(', ', $this->getErrors($contact))); 
        
        // Min 6
        $contact = $this->getEntity();
        $contact->setEmail('d@f.c');
        $this->assertStringContainsString(
            "L'e-mail doit comporter au moins 6 caractères",
            implode(', ', $this->getErrors($contact))); 

        // Max 50
        $contact = $this->getEntity();
        $contact->setEmail('@'.$this->getText(50));
        $this->assertStringContainsString(
            "L'e-mail ne peut pas comporter plus de 50 caractères",
            implode(', ', $this->getErrors($contact))); 
    }

    public function testFirstname()
    {
        $contact = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "Mike",
            $contact->getFirstname());

        // Non vide
        $user = $this->getEntity();
        $contact->setFirstname('');
        $this->assertStringContainsString(
            "Le prénom doit comporter au moins 3 caractères",
            implode(', ', $this->getErrors($contact))); 
        
        // Avec chiffre
        $contact = $this->getEntity();
        $contact->setFirstname('1Mike');
        $this->assertStringContainsString(
            "Le prénom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($contact)));

        // Avec symbole
        $contact = $this->getEntity();
        $contact->setFirstname('%Mike');
        $this->assertStringContainsString(
            "Le prénom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($contact)));
        
        // Min 3
        $contact = $this->getEntity();
        $contact->setFirstname('ik');
        $this->assertStringContainsString(
            "Le prénom doit comporter au moins 3 caractères",
            implode(', ', $this->getErrors($contact))); 

        // Max 20
        $contact = $this->getEntity();
        $contact->setFirstname($this->getText(21));
        $this->assertStringContainsString(
            "Le prénom ne peut pas comporter plus de 20 caractères",
            implode(', ', $this->getErrors($contact))); 
    }

    public function testLastname()
    {
        $contact = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "Un nom de famille",
            $contact->getLastname());

        // Non vide
        $contact = $this->getEntity();
        $contact->setLastname('');
        $this->assertStringContainsString(
            "Le nom doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($contact))); 

        // Avec chiffre
        $contact = $this->getEntity();
        $contact->setLastname('1Mike');
        $this->assertStringContainsString(
            "Le nom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($contact)));

        // Avec symbole
        $contact = $this->getEntity();
        $contact->setLastname('%Mike');
        $this->assertStringContainsString(
            "Le nom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($contact)));
        
        // Min 5
        $contact = $this->getEntity();
        $contact->setLastname('ik');
        $this->assertStringContainsString(
            "Le nom doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($contact))); 

        // Max 30
        $contact = $this->getEntity();
        $contact->setLastname($this->getText(31));
        $this->assertStringContainsString(
            "Le nom ne peut pas comporter plus de 30 caractères",
            implode(', ', $this->getErrors($contact))); 
    }

    public function testMessage()
    {
        $contact = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
          "Ceci est un petit message",
          $contact->getMessage());

        // Non vide
        $contact = $this->getEntity();
        $contact->setMessage('');
        $this->assertStringContainsString(
            "Le message doit comporter au moins 20 caractères",
            implode(', ', $this->getErrors($contact)));
        
        // Min 20
        $contact = $this->getEntity();
        $contact->setMessage($this->getText(19));
        $this->assertStringContainsString(
            "Le message doit comporter au moins 20 caractères",
            implode(', ', $this->getErrors($contact)));
        
        // Max 200
        $contact = $this->getEntity();
        $contact->setMessage($this->getText(201));
        $this->assertStringContainsString(
            "Le message ne peut pas comporter plus de 200 caractères",
            implode(', ', $this->getErrors($contact)));
    }

}