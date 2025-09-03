<?php

namespace App\Entity;

use App\Entity\Enum\MediaAssetType;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class MediaAsset
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $owner = null;

    #[ORM\Column(type: 'string', enumType: MediaAssetType::class)]
    private MediaAssetType $type;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $originalName;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private string $mime;

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive]
    private int $size;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank]
    private string $storagePath;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank]
    private string $sha256;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $meta = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // ... getters and setters ...
}
