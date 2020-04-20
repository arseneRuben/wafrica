<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeSheetRepository")
 */
class LikeSheet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")   
     */
    private $liking;  // 0--> dislike   and   1--> like

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="likesheets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="smallint")
     */
    private $targetEntityType;   // 1-> Topic and 2-> Participation

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic", inversedBy="likeSheets")
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participation", inversedBy="likeSheets")
     */
    private $participation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLiking(): ?bool
    {
        return $this->liking;
    }

    public function setLiking(bool $liking): self
    {
        $this->liking = $liking;

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

    public function getTargetEntityType(): ?int
    {
        return $this->targetEntityType;
    }

    public function setTargetEntityType(int $targetEntityType): self
    {
        $this->targetEntityType = $targetEntityType;

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

    public function getParticipation(): ?Participation
    {
        return $this->participation;
    }

    public function setParticipation(?Participation $participation): self
    {
        $this->participation = $participation;

        return $this;
    }
}
