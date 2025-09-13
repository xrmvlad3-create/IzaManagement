<?php

namespace App\Entity;

use App\Repository\SpecialtyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialtyRepository::class)]
class Specialty
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, ClinicalCase>
     */
    #[ORM\OneToMany(targetEntity: ClinicalCase::class, mappedBy: 'speciality')]
    private Collection $clinicalCases;

    public function __construct()
    {
        $this->clinicalCases = new ArrayCollection();
    }

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

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, ClinicalCase>
     */
    public function getClinicalCases(): Collection
    {
        return $this->clinicalCases;
    }

    public function addClinicalCase(ClinicalCase $clinicalCase): static
    {
        if (!$this->clinicalCases->contains($clinicalCase)) {
            $this->clinicalCases->add($clinicalCase);
            $clinicalCase->setSpeciality($this);
        }

        return $this;
    }

    public function removeClinicalCase(ClinicalCase $clinicalCase): static
    {
        if ($this->clinicalCases->removeElement($clinicalCase)) {
            // set the owning side to null (unless already changed)
            if ($clinicalCase->getSpeciality() === $this) {
                $clinicalCase->setSpeciality(null);
            }
        }

        return $this;
    }
}
