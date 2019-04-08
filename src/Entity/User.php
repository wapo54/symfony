<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *     })
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 * @UniqueEntity(fields={"email"}, message="There is no account with this email")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    private const CIPHER= 'AES-256-CBC';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank())
     * @Assert\Length(min="4", max="180")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min="8", max="2855")
     * @Assert\Regex( pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?& ]{8,}$/",
     * message=" Password must contain minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character:"
     *
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\NotBlank()
     * @Assert\IsTrue()
     */
    private $termAccepted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(type="guid", nullable=true, unique=true)
     */
    private $activationToken;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="sharer", orphanRemoval=true)
     */
    private $pictures;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Picture", mappedBy="lovers")
     */
    private $lovedPictures;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emailVector;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->pictures = new ArrayCollection();
        $this->lovedPictures = new ArrayCollection();

        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $this->setEmailVector(
            openssl_random_pseudo_bytes($ivLength)
        );
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

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

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getTermAccepted(): ?bool
    {
        return $this->termAccepted;
    }

    public function setTermAccepted(?bool $termAccepted): self
    {
        $this->termAccepted = $termAccepted;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(?string $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

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

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setSharer($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getSharer() === $this) {
                $picture->setSharer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getLovedPictures(): Collection
    {
        return $this->lovedPictures;
    }

    public function addLovedPicture(Picture $lovedPicture): self
    {
        if (!$this->lovedPictures->contains($lovedPicture)) {
            $this->lovedPictures[] = $lovedPicture;
            $lovedPicture->addLover($this);
        }

        return $this;
    }

    public function removeLovedPicture(Picture $lovedPicture): self
    {
        if ($this->lovedPictures->contains($lovedPicture)) {
            $this->lovedPictures->removeElement($lovedPicture);
            $lovedPicture->removeLover($this);
        }

        return $this;
    }

    private function getEmailVector(): ?string
    {
        return base64_decode($this->emailVector);
    }

    /**
     * @param string $emailVector
     * @return User
     */
    private function setEmailVector(string $emailVector): self
    {
        $this->emailVector =base64_encode($emailVector);

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PostUpdate()
     */
    public function encryptEmail()
    {
        //get the email
        //create a checksum of the username
        //get the IV
        //encrypt
        //set the email back
        $this->setEmail(
            openssl_encrypt(
                $this->getEmail(),
                self::CIPHER,
                md5($this->getUsername()),
                0,
                $this->getEmailVector()
            )
        );
    }

    /**
     * @ORM\PostLoad()
     */
    public function decryptEmail()
    {
        //get the encrypted email
        //create a checksum of the username
        //get the IV
        //decrypt
        //set the email back
        $this->setEmail(
            openssl_decrypt(
                $this->getEmail(),
                self::CIPHER,
                md5($this->getUsername()),
                0,
                $this->getEmailVector()
            )
        );
    }
}
