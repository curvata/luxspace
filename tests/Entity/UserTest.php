<?php

namespace App\Tests\Entity;

use App\Entity\PasswordReset;
use App\Entity\Reservation;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{    
    use Error;
    use Text;

    /**
     * @return User
     */
    public function getEntity(): User
    {
        return (new User)
                ->setEmail('19test@test.be')
                ->setPassword('%Blabla2017')
                ->setFirstname('Bamboo')
                ->setLastname('Menezes')
                ->setBirthday(new DateTime('14-07-1988'))
                ->setCreatedAt(new DateTime())
                ->setAddress('50C1 avenue des chiens')
                ->setPostalCode('6717')
                ->setCity('Luxembourg')
                ->setPhone("00.00.00 00")
                ->setCountry('Belgique');
    }
    
    /**
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        /** @var ContainerInterfance */
        return self::$container->get('debug.validator');
    }

    public function testReservation()
    {
        $user = $this->getEntity();
        $reservation = (new Reservation())->setReference('BROL');
        $user->addReservation($reservation);
        $this->assertTrue($user->getReservations()->contains($reservation));
    }

    public function testValid()
    {
        $user = $this->getEntity();
        $this->assertCount(0,$this->getErrors($user), implode(', ', $this->getErrors($user)));
    }

    public function testCreatedDate()
    {
        $user = $this->getEntity();
        $this->assertStringContainsString(
            (new DateTime())->format('d-m-y'),
            $user->getCreatedAt()->format('d-m-y'));
    }

    public function testGetStringBrithday()
    {
        $user = $this->getEntity();
        $this->assertStringContainsString(
            "14-07-1988",
            $user->getStringBirthday());  
    }

    public function testSetStringBrithday()
    {
        $user = $this->getEntity();
        $user->setStringBirthday('14-07-2000');
        $this->assertStringContainsString(
            "14-07-00",
            $user->getBirthday()->format('d-m-y'));  
    }

    public function testPasswordReset()
    {
        $user = $this->getEntity();
        $passwordReset = new PasswordReset();
        $user->setPasswordReset($passwordReset);
        $this->assertThat($passwordReset,  $this->equalTo($user->getPasswordReset()));

    }

    public function testRoles()
    {
        $user = $this->getEntity();
        $this->assertContains(
            "ROLE_USER",
            $user->getRoles());

        $user->setRoles(['ROLE_ADMIN']);
        $this->assertContains(
            "ROLE_ADMIN",
            $user->getRoles());
    }

    public function testUsername()
    {
        $user = $this->getEntity();
        $this->assertStringContainsString(
            "19test@test.be",
            $user->getUsername());
    }

    public function testSalt()
    {
        $user = $this->getEntity();
        $this->assertNull($user->getSalt());
    }

    public function testEmail()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "19test@test.be",
            $user->getEmail());

        // Invalide
        $user = $this->getEntity();
        $user->setEmail('blablamail.be');
        $this->assertStringContainsString(
            "L'e-mail \"blablamail.be\" n'est pas valide.",
            implode(', ', $this->getErrors($user)));
        
        // Non vide
        $user = $this->getEntity();
        $user->setEmail('');
        $this->assertStringContainsString(
            "L'e-mail doit comporter au moins 6 caractères",
            implode(', ', $this->getErrors($user))); 
        
        // Min 6
        $user = $this->getEntity();
        $user->setEmail('d@f.c');
        $this->assertStringContainsString(
            "L'e-mail doit comporter au moins 6 caractères",
            implode(', ', $this->getErrors($user))); 

        // Max 50
        $user = $this->getEntity();
        $user->setEmail('@'.$this->getText(50));
        $this->assertStringContainsString(
            "L'e-mail ne peut pas comporter plus de 50 caractères",
            implode(', ', $this->getErrors($user))); 
    }

    public function testPassword()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "%Blabla2017",
            $user->getPassword());
            
        // Sans symbole
        $user = $this->getEntity();
        $user->setPassword('Beudh1250edfef');
        $this->assertStringContainsString(
            "Votre mot de passe doit contenir au minimum 8 caractères dont un symbole, un chiffre et une lettre",
            implode(', ', $this->getErrors($user)));
        
        // Sans chiffre 
        $user = $this->getEntity();
        $user->setPassword('Beudh%edfef');
        $this->assertStringContainsString(
            "Votre mot de passe doit contenir au minimum 8 caractères dont un symbole, un chiffre et une lettre",
            implode(', ', $this->getErrors($user)));

        // Sans majuscule 
        $user = $this->getEntity();
        $user->setPassword('beudh%edfef');
        $this->assertStringContainsString(
            "Votre mot de passe doit contenir au minimum 8 caractères dont un symbole, un chiffre et une lettre",
            implode(', ', $this->getErrors($user)));
        
        // Min 8 
        $user = $this->getEntity();
        $user->setPassword('1Beudh%');
        $this->assertStringContainsString(
            "Votre mot de passe doit contenir au minimum 8 caractères dont un symbole, un chiffre et une lettre",
            implode(', ', $this->getErrors($user)));
    }

    public function testFirstname()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "Bamboo",
            $user->getFirstname());

        // Non vide
        $user = $this->getEntity();
        $user->setFirstname('');
        $this->assertStringContainsString(
            "Le prénom doit comporter au moins 3 caractères",
            implode(', ', $this->getErrors($user))); 
        
        // Sans chiffre
        $user = $this->getEntity();
        $user->setFirstname('1Mike');
        $this->assertStringContainsString(
            "Le prénom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($user)));

        // Avec symbole
        $user = $this->getEntity();
        $user->setFirstname('%Mike');
        $this->assertStringContainsString(
            "Le prénom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($user)));
        
        // Min 3
        $user = $this->getEntity();
        $user->setFirstname('ik');
        $this->assertStringContainsString(
            "Le prénom doit comporter au moins 3 caractères",
            implode(', ', $this->getErrors($user))); 

        // Max 20
        $user = $this->getEntity();
        $user->setFirstname($this->getText(21));
        $this->assertStringContainsString(
            "Le prénom ne peut pas comporter plus de 20 caractères",
            implode(', ', $this->getErrors($user))); 
    }

    public function testLastname()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "Menezes",
            $user->getLastname());

        // Non vide
        $user = $this->getEntity();
        $user->setLastname('');
        $this->assertStringContainsString(
            "Le nom doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($user))); 

        // Sans symbole
        $user = $this->getEntity();
        $user->setLastname('1Mike');
        $this->assertStringContainsString(
            "Le nom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($user)));

        // Sans chiffre
        $user = $this->getEntity();
        $user->setLastname('%Mike');
        $this->assertStringContainsString(
            "Le nom ne peut contenir que des lettres, des tirets et des espaces",
            implode(', ', $this->getErrors($user)));
        
        // Min 5
        $user = $this->getEntity();
        $user->setLastname('ik');
        $this->assertStringContainsString(
            "Le nom doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($user))); 

        // Max 30
        $user = $this->getEntity();
        $user->setLastname($this->getText(31));
        $this->assertStringContainsString(
            "Le nom ne peut pas comporter plus de 30 caractères",
            implode(', ', $this->getErrors($user))); 
    }

    public function testBirthday()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "14-07-88",
            $user->getBirthday()->format('d-m-y'));
        
        // Non majeur
        $user = $this->getEntity();
        $user->setBirthday(new DateTime('14-07-2010'));
        $this->assertStringContainsString(
            "Vous devez avoir minimum 18 ans pour vous inscrire",
            implode(', ', $this->getErrors($user))); 
    }

    public function testAddress()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "50C1 avenue des chiens",
            $user->getAddress());

        // Non vide
        $user = $this->getEntity();
        $user->setAddress('');
        $this->assertStringContainsString(
            "L'adresse doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($user))); 
        
        // Min 5
        $user = $this->getEntity();
        $user->setAddress('ik');
        $this->assertStringContainsString(
            "L'adresse doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($user))); 

        // Max 60
        $user = $this->getEntity();
        $user->setAddress($this->getText(61));
        $this->assertStringContainsString(
            "L'adresse ne peut pas comporter plus de 60 caractères",
            implode(', ', $this->getErrors($user))); 
    }

    public function testPostalCode()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
           "6717",
           $user->getPostalCode());

        // Non vide
        $user = $this->getEntity();
        $user->setPostalCode('');
        $this->assertStringContainsString(
            "Le code postal doit comporter au moins 3 caractères",
            implode(', ', $this->getErrors($user))); 
        
        // Min 5
        $user = $this->getEntity();
        $user->setPostalCode('11');
        $this->assertStringContainsString(
            "Le code postal doit comporter au moins 3 caractères",
            implode(', ', $this->getErrors($user))); 

        // Max 10
        $user = $this->getEntity();
        $user->setPostalCode($this->getText(11));
        $this->assertStringContainsString(
            "Le code postal ne peut pas comporter plus de 10 caractères",
            implode(', ', $this->getErrors($user))); 
    }

    public function testCity()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "Luxembourg",
            $user->getCity());

        // Non vide
        $user = $this->getEntity();
        $user->setCity('');
        $this->assertStringContainsString(
            "La ville doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($user))); 
        
        // Min 5
        $user = $this->getEntity();
        $user->setCity('AAAA');
        $this->assertStringContainsString(
            "La ville doit comporter au moins 5 caractères",
            implode(', ', $this->getErrors($user))); 

        // Max 20
        $user = $this->getEntity();
        $user->setCity($this->getText(21));
        $this->assertStringContainsString(
            "La ville ne peut pas comporter plus de 20 caractères",
            implode(', ', $this->getErrors($user))); 
    }

    public function testCountry()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "Belgique",
            $user->getCountry());

        // Non vide
        $user = $this->getEntity();
        $user->setCountry('');
        $this->assertStringContainsString(
            "Le pays doit comporter au moins 2 caractères",
            implode(', ', $this->getErrors($user))); 
        
        // Min 2
        $user = $this->getEntity();
        $user->setCountry('A');
        $this->assertStringContainsString(
            "Le pays doit comporter au moins 2 caractères",
            implode(', ', $this->getErrors($user))); 

        // Max 40
        $user = $this->getEntity();
        $user->setCountry($this->getText(41));
        $this->assertStringContainsString(
            "Le pays ne peut pas comporter plus de 40 caractères",
            implode(', ', $this->getErrors($user))); 
    }

    public function testPhone()
    {
        $user = $this->getEntity();

        // Obtenir
        $this->assertStringContainsString(
            "00.00.00 00",
            $user->getPhone());

        // Avec des caractères
        $user = $this->getEntity();
        $user->setPhone('0000hello000');
        $this->assertStringContainsString(
            "Le numéro de téléphone ne doit contenir qu'entre 8 et 20 chiffres",
            implode(', ', $this->getErrors($user))); 
        
        // Min 8
        $user = $this->getEntity();
        $user->setPhone('0000000');
        $this->assertStringContainsString(
            "Le numéro de téléphone ne doit contenir qu'entre 8 et 20 chiffres",
            implode(', ', $this->getErrors($user))); 

        // Max 30
        $user = $this->getEntity();
        $user->setPhone('0000000000000000000000000000000');
        $this->assertStringContainsString(
            "Le numéro de téléphone ne doit contenir qu'entre 8 et 20 chiffres",
            implode(', ', $this->getErrors($user))); 
    }
}