<?php

namespace App\DataFixtures\Tests;

use App\Entity\Departure;
use App\Entity\Location;
use App\Entity\Passenger;
use App\Entity\PasswordReset;
use App\Entity\Picture;
use App\Entity\Reservation;
use App\Entity\Returned;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    
    /**
     * Créer une destination
     */
    public function getDestination(string $destination): Location
    {
        return (new Location)
            ->setTitle($destination)
            ->setShortDescription('Petite description de la planète '.$destination)
            ->setDescription('Grosse description de la planète '.$destination)
            ->setCreatedAt(new DateTime())
            ->setPictureHeader("BLABLA.JPEG")
            ->setSlug('SLUG-'.$destination);
    }
    
    /**
     * Créer un vol de départ
     */
    public function getDeparture(Location $destination, string $reference, DateTime $date): Departure
    {
        return (new Departure)
            ->setReference($reference)
            ->setDate($date)
            ->setSeat(50)
            ->setRocket('FALCON')
            ->setDuration(50)
            ->setPrice(1000)
            ->setDestination($destination);
    }
    
    /**
     * Créer un vol de retour
     */
    public function getReturned(Location $destination, string $reference, DateTime $date): Returned
    {
        return (new Returned)
            ->setReference($reference)
            ->setDate($date)
            ->setSeat(50)
            ->setRocket('FALCON')
            ->setDuration(50)
            ->setPrice(1000)
            ->setFfrom($destination);
    }
    
    /**
     * Créer une réservation
     */
    public function getReservation(string $reference, User $user, Departure $departure, Returned $returned, int $status): Reservation
    {
        return (new Reservation)
            ->setStatus($status)
            ->setReference($reference)
            ->setClient($user)
            ->setCreateAt(new DateTime())
            ->setStripeReference('stripe'.$reference)
            ->setDeparture($departure)
            ->setReturned($returned)
            ->setDeparturePrice($departure->getPrice())
            ->setReturnPrice($returned->getPrice());
    }
    
    /**
     * Créer un utilisateur
     */
    public function getUser(int $id, string $role): User
    {
        return (new User)
            ->setEmail('user'.$id.'@user.be')
            ->setPassword('%Password00')
            ->setLastname('UnNom')
            ->setFirstname('UnPrenom')
            ->setBirthday(new DateTime('14-07-1988'))
            ->setRoles([$role])
            ->setCreatedAt(new DateTime())
            ->setAddress('Rue de Luxembourg')
            ->setPostalCode('0000')
            ->setCountry('Luxembourg')
            ->setCity('LuxembourgCity')
            ->setPhone(66111111);
    }
    
    /**
     * Créer un passager
     */
    public function getPassenger(): Passenger
    {
        return (new Passenger)
            ->setLastname('UnNomPassager')
            ->setFirstname('UnPrenomPassager');
    }
    
    public function load(ObjectManager $manager): void
    {
        // Destinations
        $destinationMars = $this->getDestination('Mars');
        $manager->persist($destinationMars);
        $destinationLune = $this->getDestination('Lune');
        $manager->persist($destinationLune);
        $destinationJupiter = $this->getDestination('Jupiter');
        $manager->persist($destinationJupiter);
        $destinationSaturn = $this->getDestination('Saturn');
        $manager->persist($destinationSaturn);
        $destinationVenus = $this->getDestination('Vénus');
        $manager->persist($destinationVenus);
        $destinationTitan = $this->getDestination('Titan');
        $manager->persist($destinationTitan);

        $manager->flush();

        // Images
        $picture = (new Picture())->setFilename('mars.jpeg')->setLocation($destinationMars);
        $manager->persist($picture);

        // Vols de départs
        $departureMars = $this->getDeparture($destinationMars, 'LU0000', (new DateTime())->modify('+1 days'));
        $manager->persist($departureMars);
        $departureMars2 = $this->getDeparture($destinationMars, 'LU0010', (new DateTime())->modify('+1 days'));
        $manager->persist($departureMars2);

        $departureLune = $this->getDeparture($destinationLune, 'LU0001', (new DateTime())->modify('+1 days'));
        $manager->persist($departureLune);
        $departureLune2 = $this->getDeparture($destinationLune, 'LU0011', (new DateTime())->modify('+1 days'));
        $manager->persist($departureLune2);

        $departureJupiter = $this->getDeparture($destinationJupiter, 'LU0002', (new DateTime())->modify('+1 days'));
        $manager->persist($departureJupiter);
        $departureJupiter2 = $this->getDeparture($destinationJupiter, 'LU0022', (new DateTime())->modify('+1 days'));
        $manager->persist($departureJupiter2);
        
        $departureSaturn = $this->getDeparture($destinationSaturn, 'LU0004', (new DateTime())->modify('+1 days'));
        $manager->persist($departureSaturn);
        $departureSaturn2 = $this->getDeparture($destinationSaturn, 'LU0044', (new DateTime())->modify('+1 days'));
        $manager->persist($departureSaturn2);
        
        $departureVenus = $this->getDeparture($destinationVenus, 'LU0005', (new DateTime())->modify('+1 days'));
        $manager->persist($departureVenus);
        $departureVenus2 = $this->getDeparture($destinationVenus, 'LU0015', (new DateTime())->modify('+1 days'));
        $manager->persist($departureVenus2);
        
        $departureTitan = $this->getDeparture($destinationTitan, 'LU0006', (new DateTime())->modify('+1 days'));
        $manager->persist($departureTitan);

        // Vols de retours
        $returnedMars = $this->getReturned($destinationMars, 'LU0019', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedMars);
        $returnedMars2 = $this->getReturned($destinationMars, 'LU0014', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedMars2);

        $returnedLune = $this->getReturned($destinationLune, 'LU0001', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedLune);
        $returnedLune2 = $this->getReturned($destinationLune, 'LU0011', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedLune2);

        $returnedJupiter = $this->getReturned($destinationJupiter, 'LU0002', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedJupiter);
        $returnedJupiter2 = $this->getReturned($destinationJupiter, 'LU0022', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedJupiter2);
        
        $returnedSaturn = $this->getReturned($destinationSaturn, 'LU0004', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedSaturn);
        $returnedSaturn2 = $this->getReturned($destinationSaturn, 'LU0044', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedSaturn2);
        
        $returnedVenus = $this->getReturned($destinationVenus, 'LU0005', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedVenus);
        $returnedVenus = $this->getReturned($destinationVenus, 'LU0005', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedVenus);
        
        $returnedTitan = $this->getReturned($destinationTitan, 'LU0006', (new DateTime())->modify('+2 days'));
        $manager->persist($returnedTitan);

        $manager->flush();

        // Password Reset

        $passwordReset = (new PasswordReset())->setToken('_TOKEN')->setCreatedAt(new DateTime('14-07-1988'));

        // Utilisateurs
        $user1 = $this->getUser(1, 'ROLE_ADMIN');
        $user1->setPasswordReset($passwordReset);
        $manager->persist($user1);

        $user2 = $this->getUser(2, 'ROLE_USER');
        $manager->persist($user2);

        $user3 = $this->getUser(3, 'ROLE_USER');
        $user3->setCreatedAt(new DateTime('14-07-2000'));
        $manager->persist($user3);

        // Passagers
        $passenger = $this->getPassenger();

        // Réservations
        $reservation1 = $this->getReservation('REF1', $user1, $departureMars, $returnedMars, 0);
        $reservation1->addPassenger($passenger);
        $manager->persist($reservation1);
        $reservation2 = $this->getReservation('REF2', $user1, $departureMars, $returnedMars, 1);
        $manager->persist($reservation2);
        $reservation3 = $this->getReservation('REF3', $user1, $departureJupiter, $returnedJupiter, 0);
        $manager->persist($reservation3);
        $reservation4 = $this->getReservation('REF4', $user1, $departureJupiter, $returnedJupiter, 2);
        $manager->persist($reservation4);

        $reservation5 = $this->getReservation('REF5', $user2, $departureVenus, $returnedVenus, 1);
        $manager->persist($reservation5);
        $reservation6 = $this->getReservation('REF6', $user2, $departureSaturn, $returnedSaturn, 1);
        $manager->persist($reservation6);
        $reservation7 = $this->getReservation('REF7', $user2, $departureLune, $returnedLune, 1);
        $manager->persist($reservation7);
        $reservation8 = $this->getReservation('REF8', $user2, $departureLune, $returnedLune, 1);
        $manager->persist($reservation8);
        
        $manager->flush();
    }
}
