<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome_matiere = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'matieres')]
    private Collection $user;

    #[ORM\ManyToMany(targetEntity: Formation::class, inversedBy: 'matieres')]
    private Collection $formation;

    #[ORM\ManyToMany(targetEntity: Cours::class, mappedBy: 'matiere')]
    private Collection $cours;

    public function __construct()
    {
        $this->uder_id = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->formation = new ArrayCollection();
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomeMatiere(): ?string
    {
        return $this->nome_matiere;
    }

    public function setNomeMatiere(string $nome_matiere): self
    {
        $this->nome_matiere = $nome_matiere;

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(user $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(user $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormation(): Collection
    {
        return $this->formation;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formation->contains($formation)) {
            $this->formation->add($formation);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        $this->formation->removeElement($formation);

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
            $cour->addMatiere($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            $cour->removeMatiere($this);
        }

        return $this;
    }

    public function __toString(){ // Pour afficher les selects dans le champ matiere dans easy admin
        return $this->getNomeMatiere();
    }
}
