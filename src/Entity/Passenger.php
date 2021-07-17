<?php

namespace App\Entity;

use App\Repository\PassengerRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PassengerRepository::class)
 */
class Passenger
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
    #[Assert\NotBlank(message: "Merci de renseigner votre nom")]
    #[Assert\Regex('/^[a-zA-Z\-\s]+$/', message: "Le nom ne peut contenir que des lettres, des tirets et des espaces")]
    #[Assert\Length(
        min: 5,
        max: 30,
        minMessage: "Le nom doit comporter au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "Merci de renseigner votre prénom")]
    #[Assert\Regex('/^[a-zA-Z\-\s]+$/', message: "Le prénom ne peut contenir que des lettres, des tirets et des espaces")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Le prénom doit comporter au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $firstname;

    /**
     * @ORM\ManyToOne(targetEntity=Reservation::class, inversedBy="passengers")
     */
    private Reservation $reservation;
    
    public function __toString(): string
    {
        return $this->lastname . ' ' . $this->firstname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getReservation(): Reservation
    {
        return $this->reservation;
    }

    public function setReservation($reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }
}
