<?php

namespace App\Entity;

use App\Repository\PasswordResetRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PasswordResetRepository::class)
 */
class PasswordReset
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
    private string $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="passwordReset", cascade={"persist", "remove"})
     */
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        if ($user === null && $this->user !== null) {
            $this->user->setPasswordReset(null);
        }

        if ($user !== null && $user->getPasswordReset() !== $this) {
            $user->setPasswordReset($this);
        }

        $this->user = $user;

        return $this;
    }
}
