<?php

namespace App\Entity;

use App\Repository\TourDateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TourDateRepository::class)]
class TourDate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La date est obligatoire.')]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La ville est obligatoire.')]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $salle = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Assert\Url(message: 'Le lien de billetterie doit être une URL valide.')]
    private ?string $lienBilletterie = null;

    #[ORM\ManyToOne(inversedBy: 'tourDates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getSalle(): ?string
    {
        return $this->salle;
    }

    public function setSalle(?string $salle): static
    {
        $this->salle = $salle;

        return $this;
    }

    public function getLienBilletterie(): ?string
    {
        return $this->lienBilletterie;
    }

    public function setLienBilletterie(?string $lienBilletterie): static
    {
        $this->lienBilletterie = $lienBilletterie;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
