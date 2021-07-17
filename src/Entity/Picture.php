<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 */
class Picture
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
    private string $filename;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="pictures")
     * @ORM\JoinColumn(nullable=false)
     */
    private Location $location;
        
    private ?File $pictureFile = null;

    public function __toString(): string
    {
        return $this->filename;
    }
    
    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }
    
    public function setPictureFile(File $pictureFile): self
    {
        $this->pictureFile = $pictureFile;

        return $this;
    }
    
    /**
     * Retourne le nom pour l'image version thumbnail
     */
    public function getSmallPicture(): string
    {
        return 'small_'.$this->filename;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }
}
