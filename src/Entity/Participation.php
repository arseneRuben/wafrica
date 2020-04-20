<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationRepository")
 */
class Participation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic", inversedBy="participations" ,cascade={"persist"}))
     * @ORM\JoinColumn(nullable=false)
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="participations",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LikeSheet", mappedBy="participation")
     */
    private $likeSheets;

    public function getId(): ?int
    {
        return $this->id;
       
    }

    public function getLike(): ?int
    {
        $like = 0;
        foreach ($this->likeSheets as $item) {
            if($item->getLiking())
                $like++;
        }
        return $like;
    }

    public function getDisLike(): ?int
    {
        $like = 0;
        foreach ($this->likeSheets as $item) {
            if(!$item->getLiking())
                $like++;
        }
        return $like;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

   

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;

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

    public function __construct()
    {
        $this->likeSheets = new ArrayCollection();
    }

     /**
     *
     * @return string
     */
    public function __toString() {
        $lastname = ( is_null($this->getContent())) ? "" : $this->getContent();
        
        return " " . $lastname ;
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
            $likeSheet->setParticipation($this);
        }

        return $this;
    }

    public function removeLikeSheet(LikeSheet $likeSheet): self
    {
        if ($this->likeSheets->contains($likeSheet)) {
            $this->likeSheets->removeElement($likeSheet);
            // set the owning side to null (unless already changed)
            if ($likeSheet->getParticipation() === $this) {
                $likeSheet->setParticipation(null);
            }
        }

        return $this;
    }
}
