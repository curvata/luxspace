<?php

namespace App\Class;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    #[Assert\NotBlank(message: "Merci de renseigner votre prénom")]
    #[Assert\Regex('/^[a-zA-Z\-\s]+$/', message: "Le prénom ne peut contenir que des lettres, des tirets et des espaces")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Le prénom doit comporter au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $firstname;

    #[Assert\NotBlank(message: "Merci de renseigner votre nom")]
    #[Assert\Regex('/^[a-zA-Z\-\s]+$/', message: "Le nom ne peut contenir que des lettres, des tirets et des espaces")]
    #[Assert\Length(
        min: 5,
        max: 30,
        minMessage: "Le nom doit comporter au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $lastname;

    #[Assert\NotBlank(message: "Merci de renseigner votre e-mail")]
    #[Assert\Email(message: "L'e-mail {{ value }} n'est pas valide")]
    #[Assert\Length(
        min: 6,
        max: 50,
        minMessage: "L'e-mail doit comporter au moins {{ limit }} caractères",
        maxMessage: "L'e-mail ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $email;

    #[Assert\NotBlank(message: "Merci de renseigner votre message")]
    #[Assert\Length(
        min: 20,
        max: 200,
        minMessage: "Le message doit comporter au moins {{ limit }} caractères",
        maxMessage: "Le message ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $message;
    
    public function getMessage(): ?string
    {
        return $this->message;
    }
    
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
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
    
    public function getLastname(): ?string
    {
        return $this->lastname;
    }
    
    public function setLastname(string $lastname):self
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
}
