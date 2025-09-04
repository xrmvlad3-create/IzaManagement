<?php

namespace App\Entity;

use App\Repository\DoctorProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctorProfileRepository::class)]
class DoctorProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'doctorProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // --- AICI SUNT MODIFICĂRILE NECESARE ---

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $specialties = [];

    // --- SFÂRȘITUL MODIFICĂRILOR ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // --- AICI SUNT NOILE METODE ---

    /**
     * @return string[]
     */
    public function getSpecialties(): array
    {
        return $this->specialties;
    }

    /**
     * @param string[] $specialties
     */
    public function setSpecialties(array $specialties): static
    {
        $this->specialties = $specialties;

        return $this;
    }

    // --- SFÂRȘITUL NOILOR METODE ---
}
