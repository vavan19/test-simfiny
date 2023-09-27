<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 64)]
    private string $name;

    #[ORM\Column(type: "string", length: 256, unique: true)]
    private string $email;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $created;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $deleted;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $notes;

    // getters/setters

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): User
    {
        $this->created = $created;
        return $this;
    }

    public function getDeleted(): ?\DateTimeInterface
    {
        return $this->deleted;
    }

    public function setDeleted(?\DateTimeInterface $deleted): User
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): User
    {
        $this->notes = $notes;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}