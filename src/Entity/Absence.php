<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
class Absence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'absences')]
    private Collection $user;

    #[ORM\Column]
    private ?bool $justification_statut = null;

    #[ORM\ManyToMany(targetEntity: Cours::class)]
    private Collection $cours;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    public function isJustificationStatut(): ?bool
    {
        return $this->justification_statut;
    }

    public function setJustificationStatut(bool $justification_statut): self
    {
        $this->justification_statut = $justification_statut;

        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        $this->cours->removeElement($cour);

        return $this;
    }

    public function __toString(){ 
        return $this->getId();
    }
}
