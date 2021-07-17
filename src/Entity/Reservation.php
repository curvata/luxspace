<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    const REFSTATUS = [
        '0' => 'Non payé',
        '1' => 'Payé',
        '2' => 'Remboursé'
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $reference;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reservations", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $client;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createAt;

    /**
     * @ORM\ManyToOne(targetEntity=Departure::class, inversedBy="reservations", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private Departure $departure;

    /**
     * @ORM\ManyToOne(targetEntity=Returned::class, inversedBy="reservations", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private Returned $returned;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $stripeReference;

    /**
     * @ORM\OneToMany(targetEntity=Passenger::class, mappedBy="reservation", cascade={"persist", "remove"})
     */
    private Collection $passengers;

    /**
     * @ORM\Column(type="integer")
     */
    private int $departurePrice;

    /**
     * @ORM\Column(type="integer")
     */
    private int $returnPrice;

    public function __construct()
    {
        $this->passenger = new ArrayCollection();
        $this->createAt = new DateTime();
        $this->passengers = new ArrayCollection();
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

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getDeparture(): ?Departure
    {
        return $this->departure;
    }

    public function setDeparture(?Departure $departure): self
    {
        $this->departure = $departure;

        return $this;
    }

    public function getReturned(): ?Returned
    {
        return $this->returned;
    }

    public function setReturned(?Returned $returned): self
    {
        $this->returned = $returned;

        return $this;
    }

    public function getStatus(): ?string
    {
        if (isset(self::REFSTATUS[$this->status])) {
            return self::REFSTATUS[$this->status];
        }
    }

    public function setStatus(int $status): self
    {
        if (isset(self::REFSTATUS[$status])) {
            $this->status = $status;
        }

        return $this;
    }

    public function getStripeReference(): string
    {
        return $this->stripeReference;
    }

    public function setStripeReference(string $stripeReference): self
    {
        $this->stripeReference = $stripeReference;

        return $this;
    }

    public function getPassengers(): ?Collection
    {
        return $this->passengers;
    }

    public function addPassenger(Passenger $passenger): self
    {
        if (!$this->passengers->contains($passenger)) {
            $this->passengers[] = $passenger;
            $passenger->setReservation($this);
        }

        return $this;
    }

    public function removePassenger(Passenger $passenger): self
    {
        if ($this->passengers->removeElement($passenger)) {
            $passenger->setReservation(null);
        }

        return $this;
    }

    public function getDeparturePrice(): ?int
    {
        return $this->departurePrice;
    }

    public function setDeparturePrice(int $departurePrice): self
    {
        $this->departurePrice = $departurePrice;

        return $this;
    }

    public function getReturnPrice(): ?int
    {
        return $this->returnPrice;
    }

    public function setReturnPrice(int $returnPrice): self
    {
        $this->returnPrice = $returnPrice;

        return $this;
    }
}
