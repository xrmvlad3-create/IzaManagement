<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class AIConsultation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: ClinicalCase::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ClinicalCase $clinicalCase = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $question;

    #[ORM\Column(length: 100)]
    private string $model;

    #[ORM\Column(type: 'json')]
    private array $inputsMeta = [];

    #[ORM\Column(type: 'text')]
    private string $rawPrompt;

    #[ORM\Column(type: 'json')]
    private array $aiResponse = [];

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $disclaimerAcceptedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // ... getters and setters ...
}
