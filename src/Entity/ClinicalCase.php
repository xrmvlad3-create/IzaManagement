<?php

namespace App\Entity;

use App\Repository\ClinicalCaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClinicalCaseRepository::class)]
#[ORM\Table(name: 'clinical_case')]
#[ORM\HasLifecycleCallbacks]
class ClinicalCase
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private string $specialty = 'dermatologie';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $symptoms = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $treatmentProtocol = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'clinicalCase', targetEntity: Procedure::class, cascade: ['persist', 'remove'])]
    private Collection $procedures;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->procedures = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void { $this->createdAt = new \DateTimeImmutable(); $this->updatedAt = new \DateTimeImmutable(); }
    #[ORM\PreUpdate]
    public function onPreUpdate(): void { $this->updatedAt = new \DateTimeImmutable(); }

    public function getId(): ?UuidInterface { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getSpecialty(): string { return $this->specialty; }
    public function setSpecialty(string $specialty): static { $this->specialty = $specialty; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getSymptoms(): ?string { return $this->symptoms; }
    public function setSymptoms(?string $symptoms): static { $this->symptoms = $symptoms; return $this; }
    public function getTreatmentProtocol(): ?string { return $this->treatmentProtocol; }
    public function setTreatmentProtocol(?string $protocol): static { $this->treatmentProtocol = $protocol; return $this; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function setCreatedBy(?User $user): static { $this->createdBy = $user; return $this; }
    public function getProcedures(): Collection { return $this->procedures; }
    public function addProcedure(Procedure $procedure): static { if (!$this->procedures->contains($procedure)) { $this->procedures->add($procedure); $procedure->setClinicalCase($this); } return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
}
