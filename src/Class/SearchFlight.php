<?php

namespace App\Class;

use App\Entity\Location;
use DateTime;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

class SearchFlight
{
    private Location $destination;

    #[Assert\GreaterThanOrEqual("today UTC+2", message:"Date antérieure")]
    private DateTime $departure;
    
    #[Assert\GreaterThan(propertyPath: "departure", message:"Date antérieure au départ")]
    private DateTime $returned;

    #[Assert\Range(min: 1, max: 9, notInRangeMessage: "Invalide")]
    private int $passagers;

    public function getDestination(): ?Location
    {
        return $this->destination;
    }

    public function setDestination(Location $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDeparture(): ?DateTime
    {
        return $this->departure;
    }

    public function setDeparture(DateTime|string $departure): self
    {
        if (!$departure instanceof DateTime) {
            $departure = $this->transformInDate($departure);
        }

        $this->departure = $departure;

        return $this;
    }

    public function getReturned(): ?DateTime
    {
        return $this->returned;
    }

    public function setReturned(DateTime|string $returned): self
    {
        if (!$returned instanceof DateTime) {
            $returned = $this->transformInDate($returned);
        }

        $this->returned = $returned;

        return $this;
    }

    public function getPassagers(): ?int
    {
        return $this->passagers;
    }

    public function setPassagers(int $passagers): self
    {
        $this->passagers = $passagers;

        return $this;
    }

    public function transformInDate(string $date): DateTime
    {
        try {
            return new DateTime($date);
        } catch (Exception $e) {
            return new DateTime();
        }
    }
}
