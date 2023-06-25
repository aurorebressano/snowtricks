<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?trick $trick = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $publisher = null;

    #[Assert\File(
        extensions: ['png', 'jpg', 'jpeg'],
        extensionsMessage: 'Please upload a valid file (png, jpeg, jpg)',
    )]
    private ?File $file = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $header = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getTrick(): ?trick
    {
        return $this->trick;
    }

    public function setTrick(?trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getPublisher(): ?user
    {
        return $this->publisher;
    }

    public function setPublisher(?user $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }
    
    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getPath();
    }

    public function isHeader(): ?bool
    {
        return $this->header;
    }

    public function setHeader(bool $header): self
    {
        $this->header = $header;

        return $this;
    }
}
