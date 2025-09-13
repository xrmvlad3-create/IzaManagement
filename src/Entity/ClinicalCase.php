<?php

namespace App\Entity;

use App\Repository\ClinicalCaseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClinicalCaseRepository::class)]
class ClinicalCase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $name = null;

    #[ORM\Column(length: 50000, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'clinicalCases')]
    private ?Specialty $speciality = null;

    public function __toString(): string {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSpeciality(): ?Specialty
    {
        return $this->speciality;
    }

    public function setSpeciality(?Specialty $speciality): static
    {
        $this->speciality = $speciality;

        return $this;
    }
}
