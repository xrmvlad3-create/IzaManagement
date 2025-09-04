<?php

namespace App\Entity;

use App\Entity\Enum\CaseStatus;
use App\Repository\ClinicalCaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
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
    #[Groups(['case:read'])]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Numele cazului nu poate fi gol.')]
    #[Groups(['case:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['case:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['case:read'])]
    private ?string $symptoms = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['case:read'])]
    private ?string $treatmentProtocol = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['case:read'])]
    private array $procedureCodes = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['case:read'])]
    private ?string $guides = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['case_read_detailed'])] // More restricted group
    private array $media = [];

    #[ORM\Column(type: 'string', length: 50, enumType: CaseStatus::class)]
    #[Groups(['case:read'])]
    private CaseStatus $status = CaseStatus::DRAFT;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['case_read_detailed'])]
    private ?User $createdBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['admin:read'])] // Only for admins
    private ?string $auditLog = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['case:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['case:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidInterface { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getSymptoms(): ?string { return $this->symptoms; }
    public function setSymptoms(?string $symptoms): static { $this->symptoms = $symptoms; return $this; }
    public function getTreatmentProtocol(): ?string { return $this->treatmentProtocol; }
    public function setTreatmentProtocol(?string $treatmentProtocol): static { $this->treatmentProtocol = $treatmentProtocol; return $this; }
    public function getProcedureCodes(): array { return $this->procedureCodes; }
    public function setProcedureCodes(array $procedureCodes): static { $this->procedureCodes = $procedureCodes; return $this; }
    public function getGuides(): ?string { return $this->guides; }
    public function setGuides(?string $guides): static { $this->guides = $guides; return $this; }
    public function getMedia(): array { return $this->media; }
    public function setMedia(array $media): static { $this->media = $media; return $this; }
    public function getStatus(): CaseStatus { return $this->status; }
    public function setStatus(CaseStatus $status): static { $this->status = $status; return $this; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function setCreatedBy(?User $createdBy): static { $this->createdBy = $createdBy; return $this; }
    public function getAuditLog(): ?string { return $this->auditLog; }
    public function addAuditLogEntry(string $logEntry, string $userEmail): static { $timestamp = (new \DateTimeImmutable())->format('Y-m-d H:i:s'); $newLog = sprintf("[%s] User: %s - Action: %s", $timestamp, $userEmail, $logEntry); $this->auditLog = empty($this->auditLog) ? $newLog : $this->auditLog . PHP_EOL . $newLog; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
}
