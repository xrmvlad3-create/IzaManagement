<?php

namespace App\Entity;

use App\Entity\Enum\ProtocolIntent;
use App\Entity\Enum\ProtocolStatus;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Protocol
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: DermCondition::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private DermCondition $condition;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', enumType: ProtocolIntent::class)]
    private ProtocolIntent $intent;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $audience = null;

    #[ORM\Column(type: 'json')]
    private array $steps = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $meds = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $contraindications = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $evidenceLevel = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $attachments = null;

    #[ORM\Column(type: 'string', enumType: ProtocolStatus::class)]
    private ProtocolStatus $status = ProtocolStatus::DRAFT;

    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private int $version = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ... getters and setters ...
}
