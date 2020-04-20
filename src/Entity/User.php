<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity ;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Vich\Uploadable
 * @UniqueEntity(
 * fields = {"email"},
 * message = "L'email que vous indiquez deja utilisee"
 * )
 */
class User implements UserInterface, \Serializable
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     *
     */
    private $filename;

      /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="topic_author", fileNameProperty="filename")
     * 
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @ORM\Column(type="string", length=100)
     */
    private $email;

     /**
     * @Assert\Length(
     *      min = 8,
     *      max = 20,
     *      minMessage = "Votre mot de passe doit faire au minimum 8 caracteres",
     *      maxMessage = "Pas besoins d'un si long mot de passe",
     *      allowEmptyString = false)
     * @Assert\EqualTo( propertyPath="confirm_password",
     * message = " Le mot de passe et le mot de passe de verification doivent etre les memes ")
     * @ORM\Column(type="string", length=20)
     */
    private $password;
    
    public $confirm_password;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $expertise;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participation", mappedBy="author")
     */
    private $participations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Topic", mappedBy="author")
     */
    private $topics;

     /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LikeSheet", mappedBy="author", orphanRemoval=true)
     */
    private $likesheets;


   
     /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
 
        return array_unique($roles);
    }
 
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
 
        return $this;
    }
 
    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->topics = new ArrayCollection();
        $this->roles = [self::ROLE_USER];
        $this->likesheets = new ArrayCollection();
    
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getExpertise(): ?string
    {
        return $this->expertise;
    }

    public function setExpertise(?string $expertise): self
    {
        $this->expertise = $expertise;

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
            $participation->setAuthor($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            // set the owning side to null (unless already changed)
            if ($participation->getAuthor() === $this) {
                $participation->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Topic[]
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setAuthor($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->contains($topic)) {
            $this->topics->removeElement($topic);
            // set the owning side to null (unless already changed)
            if ($topic->getAuthor() === $this) {
                $topic->setAuthor(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

  
    /**
     *
     * @return string
     */
    public function __toString() {
        $lastname = ( is_null($this->getUsername())) ? "" : $this->getUsername();
        
        return " " . $lastname ;
    }

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

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function eraseCredentials()
    {
        
    }
    public function getSalt()
    {
        
    }

   
    
     /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->email,
            $this->expertise,
            $this->password,
            $this->address,
            $this->filename
        ]);
    }
 
    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list($this->id,
        $this->username,
        $this->email,
        $this->expertise,
        $this->password,
        $this->address,
        $this->filename) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return Collection|LikeSheet[]
     */
    public function getLikesheets(): Collection
    {
        return $this->likesheets;
    }

    public function addLikesheet(LikeSheet $likesheet): self
    {
        if (!$this->likesheets->contains($likesheet)) {
            $this->likesheets[] = $likesheet;
            $likesheet->setAuthor($this);
        }

        return $this;
    }

    public function removeLikesheet(LikeSheet $likesheet): self
    {
        if ($this->likesheets->contains($likesheet)) {
            $this->likesheets->removeElement($likesheet);
            // set the owning side to null (unless already changed)
            if ($likesheet->getAuthor() === $this) {
                $likesheet->setAuthor(null);
            }
        }

        return $this;
    }

  
}
