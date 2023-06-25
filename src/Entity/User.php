<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ["email"], message: "Cet email est déjà utilisé")]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotNull(message: "Veuillez renseigner un email")]
    #[Assert\Email(message: "Veuillez saisir un email valide")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotNull]
    #[SecurityAssert\UserPassword(
        message: 'Wrong value for your current password',
    )]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isValidated = false;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Veuillez renseigner votre nom")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Veuillez renseigner votre prénom")]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    private ?File $file = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $registrationDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: Trick::class, orphanRemoval: true)]
    private Collection $tricks;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'publisher', targetEntity: Picture::class, orphanRemoval: true)]
    private Collection $pictures;

    #[ORM\OneToMany(mappedBy: 'publisher', targetEntity: Video::class, orphanRemoval: true)]
    private Collection $videos;

    // #[ORM\OneToOne(mappedBy: 'user', targetEntity: Avatar::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    // private ?Avatar $avatar = null;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
        $this->registrationDate = new \DateTimeImmutable();
        $this->messages = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->isValidated = false;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeImmutable
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeImmutable $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, Trick>
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks->add($trick);
            $trick->setUser($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->removeElement($trick)) {
            // set the owning side to null (unless already changed)
            if ($trick->getUser() === $this) {
                $trick->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setPublisher($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getPublisher() === $this) {
                $picture->setPublisher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setPublisher($this);
        }
        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getPublisher() === $this) {
                $video->setPublisher(null);
            }
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     */
    public function setFile(?File $file): void
    {
        $this->file = $file;
    }

    public function __toString(): string
    {
        return $this->getName() . " " . $this->getFirstName();
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'picture' => $this->picture,
            'password' => $this->password,
        ];
    }
    // public function getAvatar(): ?Avatar
    // {
    //     return $this->avatar;
    // }

    // public function setAvatar(?Avatar $avatar): self
    // {
    //     // unset the owning side of the relation if necessary
    //     if ($avatar === null && $this->avatar !== null) {
    //         $this->avatar->setUser(null);
    //     }

    //     // set the owning side of the relation if necessary
    //     if ($avatar !== null && $avatar->getUser() !== $this) {
    //         $avatar->setUser($this);
    //     }

    //     $this->avatar = $avatar;

    //     return $this;
    // }
}
