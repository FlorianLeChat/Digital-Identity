<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Matiere::class, inversedBy: 'cours')]
    private Collection $matiere;

    #[ORM\ManyToMany(targetEntity: Formation::class, inversedBy: 'cours')]
    private Collection $formation;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'cours')]
    private Collection $user;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 16)]
    private ?string $type = null;

    #[ORM\Column]
    private ?bool $terminé = null;

    #[ORM\ManyToMany(targetEntity: Presence::class, mappedBy: 'cours')]
    private Collection $presences;

    #[ORM\Column(length: 4096)]
    private ?string $token = null;

    #[ORM\Column(nullable: true)]
    private ?int $groupe = null;

    public function __construct()
    {
        $this->matiere = new ArrayCollection();
        $this->formation = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->presences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Matiere>
     */
    public function getMatiere(): Collection
    {
        return $this->matiere;
    }

    public function addMatiere(Matiere $matiere): self
    {
        if (!$this->matiere->contains($matiere)) {
            $this->matiere->add($matiere);
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): self
    {
        $this->matiere->removeElement($matiere);

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isTerminé(): ?bool
    {
        return $this->terminé;
    }

    public function setTerminé(bool $terminé): self
    {
        $this->terminé = $terminé;

        return $this;
    }

    /**
     * @return Collection<int, Presence>
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if (!$this->presences->contains($presence)) {
            $this->presences->add($presence);
            $presence->addCour($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->removeElement($presence)) {
            $presence->removeCour($this);
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getGroupe(): ?int
    {
        return $this->groupe;
    }

    public function setGroupe(?int $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function __toString(){ // Pour afficher les selects dans le champ id de cours dans easy admin
        return $this->getId();
    }
}
