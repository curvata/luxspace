<?php

namespace App\Tests\Entity;

use App\Entity\Departure;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\Returned;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReservationTest extends KernelTestCase
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
     * @return Reservation
     */
    public function getEntity(): Reservation
    {
        return (new Reservation)
                ->setStatus(0)
                ->setReference('LU1000')
                ->setClient((new User)->setFirstname('MIKE'))
                ->setCreateAt(new DateTime())
                ->setStripeReference('STRIPEREF')
                ->setDeparture((new Departure())->setReference('DEPARTURE'))
                ->setReturned((new Returned())->setReference('RETURN'))
                ->setDeparturePrice(1000)
                ->setReturnPrice(1000);
    }

    public function testStatus()
    {
        $reservation = $this->getEntity();
        $this->assertStringContainsString('Non payé', $reservation->getStatus());
        $reservation->setStatus(1);
        $this->assertStringContainsString('Payé', $reservation->getStatus());
        $reservation->setStatus(2);
        $this->assertStringContainsString('Remboursé', $reservation->getStatus());
    }

    public function testGetters()
    {
        $reservation = $this->getEntity();
        $this->assertStringContainsString('LU1000', $reservation->getReference());
        $this->assertStringContainsString('MIKE', $reservation->getClient()->getFirstname());
        $this->assertStringContainsString((new DateTime())->format('d-m-y'), $reservation->getCreateAt()->format('d-m-y'));
        $this->assertStringContainsString('DEPARTURE', $reservation->getDeparture()->getReference());
        $this->assertStringContainsString('RETURN', $reservation->getReturned()->getReference());
        $this->assertStringContainsString('STRIPEREF', $reservation->getStripeReference());
        $this->assertEquals(1000, $reservation->getDeparturePrice());
        $this->assertEquals(1000, $reservation->getReturnPrice());
    }

    public function testPassengers()
    {
        $passenger = new Passenger();
        $passenger->setFirstname('Mike')->setLastname('toto');
        $reservation = $this->getEntity();
        $passenger->setReservation($reservation);
        $reservation->addPassenger($passenger);
        $this->assertTrue($reservation->getPassengers()->contains($passenger));
    }
}