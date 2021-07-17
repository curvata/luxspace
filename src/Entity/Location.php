<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 */
class Location
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
    #[Assert\NotBlank(message:"Merci de renseigner un titre")]
    #[Assert\Regex('/^[a-zA-Zé\-\s]+$/', message: "Le titre ne doit contenir que des lettres")]
    #[Assert\Length(
        min: 4,
        max: 30,
        minMessage: "Le titre doit comporter au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "Merci de renseigner une description courte")]
    #[Assert\Length(
        min: 20,
        max: 190,
        minMessage: "La description courte doit comporter au moins {{ limit }} caractères",
        maxMessage: "La description courte ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $shortDescription;

    /**
     * @ORM\Column(type="text")
     */
    #[Assert\NotBlank(message: "Merci de renseigner la description")]
    #[Assert\Length(
        min: 50,
        max: 950,
        minMessage: "La description doit comporter au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas comporter plus de {{ limit }} caractères"
    )]
    private string $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $pictureHeader = "placeholder.png";

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="location", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $pictures;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "Merci de renseigner le slug")]
    #[Assert\Regex('/^[a-zA-Z0-9\-\S]+$/', message: "Merci de renseigner un slug valide")]
    private string $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

     /**
      * @ORM\OneToMany(targetEntity=Departure::class, mappedBy="destination", cascade={"persist", "remove"})
      */
    private Collection $departures;

    /**
     * @ORM\OneToMany(targetEntity=Returned::class, mappedBy="ffrom", cascade={"persist", "remove"})
     */
    private Collection $returned;

    /**
     *
     * @Assert\All({
     * @Assert\Image (
     * mimeTypes={"image/jpeg","image/png"},
     * mimeTypesMessage="Seul les formats jpeg et png sont acceptés"
     * )})
     */
    private ?array $pictureFiles;
   
    #[Assert\Image(
        mimeTypes: ["image/jpeg","image/png"],
        mimeTypesMessage: "Uniquement les formats jpeg et png sont acceptés"
    )]
    private ?File $headerPictureFile = null;

    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAt(new DateTime());
        $this->pictures = new ArrayCollection();
        $this->departures = new ArrayCollection();
        $this->returnFs = new ArrayCollection();
    }
    
    public function __toString(): string
    {
        return $this->title;
    }
    
    public function getPictureFiles(): array
    {
        return $this->pictureFiles;
    }
    
    public function setPictureFiles(array $pictureFiles): self
    {
        foreach ($pictureFiles as $picturefile) {
            $picture = new Picture();
            $picture->setPictureFile($picturefile);
            $this->addPicture($picture);
        }
        
        return $this;
    }

    public function getHeaderPictureFile(): ?File
    {
        return $this->headerPictureFile;
    }

    public function setHeaderPictureFile(File $headerPictureFile): self
    {
        $this->headerPictureFile = $headerPictureFile;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPictureHeader(): ?string
    {
        return $this->pictureHeader;
    }

    public function setPictureHeader(string $pictureHeader): self
    {
        $this->pictureHeader = $pictureHeader;

        return $this;
    }

    public function getPictures(): ?Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setLocation($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            if ($picture->getLocation() === $this) {
                $picture->setLocation(null);
            }
        }

        return $this;
    }

    public function getDepartures(): ?Collection
    {
        return $this->departures;
    }

    public function addDeparture(Departure $departure): self
    {
        if (!$this->departures->contains($departure)) {
            $this->departures[] = $departure;
            $departure->setDestination($this);
        }

        return $this;
    }

    public function removeDeparture(Departure $departure): self
    {
        if ($this->departures->removeElement($departure)) {
            if ($departure->getDestination() === $this) {
                $departure->setDestination(null);
            }
        }

        return $this;
    }

    public function getReturned(): ?Collection
    {
        return $this->returned;
    }

    public function addReturnF(Returned $returned): self
    {
        if (!$this->returned->contains($returned)) {
            $this->returned[] = $returned;
            $returned->setFfrom($this);
        }

        return $this;
    }

    public function removeReturned(Returned $returned): self
    {
        if ($this->returned->removeElement($returned)) {
            if ($returned->getFfrom() === $this) {
                $returned->setFfrom(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $s = new Slugify();
        $this->slug = $s->slugify($slug);

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
}
