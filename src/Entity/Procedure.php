<?php

namespace App\Entity;

use App\Repository\ProcedureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProcedureRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Procedure
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['procedure:read'])]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['procedure:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['procedure:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['procedure:read'])]
    private array $tutorialSteps = [];

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['procedure:read'])]
    private array $videoLinks = [];

    /**
     * Câmp cheie pentru filtrare și securitate.
     */
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['procedure:read'])]
    private string $specialty;

    #[ORM\ManyToOne(targetEntity: ClinicalCase::class, inversedBy: 'procedures')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['procedure:read'])]
    private ?ClinicalCase $clinicalCase = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['procedure:read'])]
    private ?string $warnings = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['admin:read'])]
    private ?string $auditLog = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['procedure:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['procedure:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void { $this->createdAt = new \DateTimeImmutable(); $this->updatedAt = new \DateTimeImmutable(); }
    #[ORM\PreUpdate]
    public function onPreUpdate(): void { $this->updatedAt = new \DateTimeImmutable(); }

    public function getId(): ?UuidInterface { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getTutorialSteps(): array { return $this->tutorialSteps; }
    public function setTutorialSteps(array $steps): static { $this->tutorialSteps = $steps; return $this; }
    public function getVideoLinks(): array { return $this->videoLinks; }
    public function setVideoLinks(array $links): static { $this->videoLinks = $links; return $this; }
    public function getSpecialty(): string { return $this->specialty; }
    public function setSpecialty(string $specialty): static { $this->specialty = strtolower($specialty); return $this; }
    public function getClinicalCase(): ?ClinicalCase { return $this->clinicalCase; }
    public function setClinicalCase(?ClinicalCase $case): static { $this->clinicalCase = $case; return $this; }
    public function getWarnings(): ?string { return $this->warnings; }
    public function setWarnings(?string $warnings): static { $this->warnings = $warnings; return $this; }
    public function getAuditLog(): ?string { return $this->auditLog; }
    public function setAuditLog(?string $log): static { $this->auditLog = $log; return $this; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function setCreatedBy(?User $user): static { $this->createdBy = $user; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
}
