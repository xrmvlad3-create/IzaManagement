<?php

namespace App\Entity;

use App\Entity\Enum\DermConditionStatus;
use App\Repository\DermConditionRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: DermConditionRepository::class)]
#[ORM\Table(name: 'derm_conditions')]
class DermCondition
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $summary = null;

    // ... alte proprietăți ...

    #[ORM\Column(type: 'string', enumType: DermConditionStatus::class)]
    private ?DermConditionStatus $status = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    // METODA LIPSĂ PE CARE O ADĂUGĂM
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    // ... alți getteri și setteri ...

    public function getStatus(): ?DermConditionStatus
    {
        return $this->status;
    }

    public function setStatus(DermConditionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
