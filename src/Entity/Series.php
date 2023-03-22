<?php

namespace App\Entity;


use Vich\Uploadable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SeriesRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('Nom')]
#[ORM\Entity(repositoryClass: SeriesRepository::class)]
#[Vich\Uploadable]
class Series
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $Nom = null;

    #[Vich\UploadableField(mapping: 'series_images', fileNameProperty: 'imageName')]
    private ?File $imagefile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column]
    #[Assert\Positive()]
    #[Assert\NotNull()]
    #[Assert\Range(
        min: 1903 ,
        max: 2023,
        notInRangeMessage: 'You must be between {{ min }}cm and {{ max }}cm tall to enter',
    )]
    private ?int $Annee = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero()]
    #[Assert\LessThan(1000)]
    private ?int $NbComics = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]
    private ?string $Description = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isFavorite = false;

    #[ORM\Column(type: 'boolean')]
    private $isPublic = false;


    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Comics::class)]
    private Collection $Comics;

    #[ORM\ManyToOne(inversedBy: 'Series')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'series', targetEntity: Mark::class, orphanRemoval: true)]
    private Collection $marks;

    private ?float $average = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->Comics = new ArrayCollection();
        $this->marks = new ArrayCollection();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function setImagefile(?File $imagefile = null): void
    {
        $this->imagefile = $imagefile;

        if (null !== $imagefile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImagefile(): ?File
    {
        return $this->imagefile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getAnnee(): ?int
    {
        return $this->Annee;
    }

    public function setAnnee(int $Annee): self
    {
        $this->Annee = $Annee;

        return $this;
    }

    public function getNbComics(): ?int
    {
        return $this->NbComics;
    }

    public function setNbComics(?int $NbComics): self
    {
        $this->NbComics = $NbComics;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getIsFavorite(): ?bool
    {
        return $this->isFavorite;
    }

    public function setIsFavorite(bool $isFavorite): self
    {
        $this->isFavorite = $isFavorite;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

/**
     * Get the value of updatedAt
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Comics>
     */
    public function getComics(): Collection
    {
        return $this->Comics;
    }

    public function addComic(Comics $comic): self
    {
        if (!$this->Comics->contains($comic)) {
            $this->Comics->add($comic);
        }

        return $this;
    }

    public function removeComic(Comics $comic): self
    {
        $this->Comics->removeElement($comic);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Mark>
     */
    public function getMarks(): Collection
    {
        return $this->marks;
    }

    public function addMark(Mark $mark): self
    {
        if (!$this->marks->contains($mark)) {
            $this->marks->add($mark);
            $mark->setSeries($this);
        }

        return $this;
    }

    public function removeMark(Mark $mark): self
    {
        if ($this->marks->removeElement($mark)) {
            // set the owning side to null (unless already changed)
            if ($mark->getSeries() === $this) {
                $mark->setSeries(null);
            }
        }

        return $this;
    }



    /**
     * Get the value of average
     */ 
    public function getAverage()
    {
        $marks = $this->getMarks();

        if ($marks->toArray() === []) {
             $this->average = null;
             return $this->average;
        }

        $total = 0;
        $count = 0;
        foreach ($marks as $mark) {
            $total += $mark->getMark();
        }

        $this->average = $total / count($marks);

        return $this->average;
    }

    
}
