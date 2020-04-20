<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TopicRepository")
 * @Vich\Uploadable
 */
class Topic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (
     *     message = "Il ne peut avoir un Projet sans titre!"
     * )
     *
     */
    private $title;

     /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     *
     */
    private $filename;

      /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="topic_image", fileNameProperty="filename")
     * 
     * @var File|null
     */
    private $imageFile;

    

    /**
     * @ORM\Column(type="text")
    * @Assert\NotBlank (
     *     message = "Veuillez decrire brievement sur quoi porte ce projet!"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

  

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="topics")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participation", mappedBy="topic")
     */
    private $participations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="topics")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LikeSheet", mappedBy="topic")
     */
    private $likeSheets;

  

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->likeSheets = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLike(): ?int
    {
        $like = 0;
        foreach ($this->likeSheets as $item) {
            if($item->getLiking()==true)
                $like++;
        }
        return $like;
    }

    public function getDisLike(): ?int
    {
        $like = 0;
        foreach ($this->likeSheets as $item) {
            if($item->getLiking()==false)
                $like++;
        }
        return $like;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setTopic($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            // set the owning side to null (unless already changed)
            if ($participation->getTopic() === $this) {
                $participation->setTopic(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return Collection|LikeSheet[]
     */
    public function getLikeSheets(): Collection
    {
        return $this->likeSheets;
    }

    public function addLikeSheet(LikeSheet $likeSheet): self
    {
        if (!$this->likeSheets->contains($likeSheet)) {
            $this->likeSheets[] = $likeSheet;
            $likeSheet->setTopic($this);
        }

        return $this;
    }

    public function removeLikeSheet(LikeSheet $likeSheet): self
    {
        if ($this->likeSheets->contains($likeSheet)) {
            $this->likeSheets->removeElement($likeSheet);
            // set the owning side to null (unless already changed)
            if ($likeSheet->getTopic() === $this) {
                $likeSheet->setTopic(null);
            }
        }

        return $this;
    }

   

}
