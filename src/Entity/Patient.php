<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PatientRepository::class)]
class Patient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "you should tell us your Medical problem")]
    private ?string $cas_med = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "you should give CNUM Number")]
    private ?string $n_cnam = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "you should Give Your Assurance Info")]
    private ?string $assurance = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "you should Write your assurance Nubmer")]
    private ?string $num_assurance = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[Assert\NotBlank(message: "you should select the user  ")]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCasMed(): ?string
    {
        return $this->cas_med;
    }

    public function setCasMed(string $cas_med): static
    {
        $this->cas_med = $cas_med;

        return $this;
    }

    public function getNCnam(): ?string
    {
        return $this->n_cnam;
    }

    public function setNCnam(string $n_cnam): static
    {
        $this->n_cnam = $n_cnam;

        return $this;
    }

    public function getAssurance(): ?string
    {
        return $this->assurance;
    }

    public function setAssurance(string $assurance): static
    {
        $this->assurance = $assurance;

        return $this;
    }

    public function getNumAssurance(): ?string
    {
        return $this->num_assurance;
    }

    public function setNumAssurance(string $num_assurance): static
    {
        $this->num_assurance = $num_assurance;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
