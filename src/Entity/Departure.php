<?php

namespace App\Entity;

use App\Repository\DepartureRepository;
use DateTime;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DepartureRepository::class)
 */
#[UniqueEntity("reference", message: "Référence déja utilisée par un autre vol")]
class Departure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "Merci de renseigner la référence du vol")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z]{2}[0-9]{4}$/",
        match: true,
        message:"La référence doit contenir 2 lettres suivies de 4 chiffres"
    )]
    private string $reference;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Assert\NotBlank(message: "Merci de renseigner la date de départ")]
    #[Assert\GreaterThanOrEqual("today +1 day UTC+2", message:"Merci de renseigner une date valide")]
    private DateTime $date;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: "Merci de renseigner le nombre de places")]
    #[Assert\Regex('/^\d+$/', message: "Merci de renseigner un nombre")]
    #[Assert\LessThanOrEqual(1000, message: "Vous ne pouvez pas renseigner plus de {{ compared_value }} places")]
    #[Assert\GreaterThanOrEqual(50, message: "Vous ne pouvez pas renseigner moins de {{ compared_value }} places")]
    private int $seat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "Merci de renseigner le nom de la fusée")]
    #[Assert\Length(
        min: 5,
        max: 30,
        minMessage: "Le nom de la fusée doit comporter au moins {{ limit }} caractères",
        maxMessage: "Le nom de la fusée ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $rocket;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: "Merci de renseigner la durée du vol")]
    #[Assert\Regex('/^\d+$/', message: "Merci de renseigner un nombre")]
    #[Assert\LessThanOrEqual(25, message: "La durée du vol ne peut dépasser {{ compared_value }} heures")]
    private int $duration;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank(message: "Merci de renseigner le prix par personne")]
    #[Assert\Regex('/^\d+$/', message: "Merci de renseigner un nombre")]
    #[Assert\LessThanOrEqual(10000, message: "Le prix doit être inférieur à {{ compared_value }} €")]
    private int $price;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="departures")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\NotBlank(message: "Merci de renseigner la destination")]
    private Location $destination;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="departure")
     */
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->reference . " " . $this->destination . " " .$this->date->format("Y-m-d H-m-s") . " ";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSeat(): ?int
    {
        return $this->seat;
    }

    public function setSeat(int $seat): self
    {
        $this->seat = $seat;

        return $this;
    }

    public function getRocket(): ?string
    {
        return $this->rocket;
    }

    public function setRocket(string $rocket): self
    {
        $this->rocket = $rocket;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getReservations(): ?Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setDeparture($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getDeparture() === $this) {
                $reservation->setDeparture(null);
            }
        }

        return $this;
    }

    public function getDestination(): ?Location
    {
        return $this->destination;
    }

    public function setDestination(?Location $destination): self
    {
        $this->destination = $destination;

        return $this;
    }
}
