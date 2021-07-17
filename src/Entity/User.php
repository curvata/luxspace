<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[UniqueEntity("email", message: "Cette adresse e-mail est déja associée à un compte")]
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[Assert\NotBlank(message:"Merci de renseigner une adresse e-mail")]
    #[Assert\Email(message: "L'e-mail {{ value }} n'est pas valide.")]
    #[Assert\Length(
        min: 6,
        max: 50,
        minMessage: "L'e-mail doit comporter au moins {{ limit }} caractères",
        maxMessage: "L'e-mail ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    //@var string The hashed password
    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank(message: "Merci de renseigner un mot de passe")]
    #[MyAssert\Password]
    private string $password;

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
     * @ORM\Column(type="datetime")
     */
    #[Assert\NotBlank(message: "Merci de renseigner votre date de naissance")]
    #[MyAssert\Adult]
    private DateTime $birthday;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="client")
     */
    private Collection $reservations;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "Merci de renseigner votre adresse")]
    #[Assert\Length(
        min: 5,
        max: 60,
        minMessage: "L'adresse doit comporter au moins {{ limit }} caractères",
        maxMessage: "L'adresse ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $Address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "Merci de renseigner votre code postal")]
    #[Assert\Length(
        min: 3,
        max: 10,
        minMessage: "Le code postal doit comporter au moins {{ limit }} caractères",
        maxMessage: "Le code postal ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $postalCode;

    /**
     * @ORM\Column(type="string", length=255)
    */
    #[Assert\NotBlank(message: "Merci de renseigner votre ville")]
    #[Assert\Regex('/^[a-zA-Z\-\s]+$/', message: "La ville ne doit contenir que des lettres")]
    #[Assert\Length(
        min: 5,
        max: 20,
        minMessage: "La ville doit comporter au moins {{ limit }} caractères",
        maxMessage: "La ville ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "Merci de renseigner votre pays")]
    #[Assert\Regex('/^[a-zA-Z\-\s]+$/', message: "Le pays ne doit contenir que des lettres")]
    #[Assert\Length(
        min: 2,
        max: 40,
        minMessage: "Le pays doit comporter au moins {{ limit }} caractères",
        maxMessage: "Le pays ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $country;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "Merci de renseigner votre numéro de téléphone")]
    #[Assert\Regex('/^[\d\.\s]{8,30}$/', message: "Le numéro de téléphone ne doit contenir qu'entre 8 et 20 chiffres")]
    private string $phone;

    /**
     * @ORM\OneToOne(targetEntity=PasswordReset::class, inversedBy="user", cascade={"persist", "remove"})
     */
    private ?PasswordReset $passwordReset = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->createdAt = new DateTime();
    }

    public function __toString(): string
    {
        return "#".$this->id. " ". $this->lastname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getStringBirthday(): ?string
    {
        if ($this->birthday) {
            return $this->birthday->format('d-m-Y');
        }
        return null;
    }

    public function setStringBirthday($birthday): self
    {
        if (is_string($birthday)) {
            $birthday = new DateTime($birthday);
        }
        $this->birthday = $birthday;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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
            $reservation->setClient($this);
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->Address;
    }

    public function setAddress(string $Address): self
    {
        $this->Address = $Address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getClient() === $this) {
                $reservation->setClient(null);
            }
        }

        return $this;
    }

    public function getPasswordReset(): ?PasswordReset
    {
        return $this->passwordReset;
    }

    public function setPasswordReset(?PasswordReset $passwordReset): self
    {
        $this->passwordReset = $passwordReset;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
