<?php

namespace App\Entity;

use App\Repository\PresenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PresenceRepository::class)]
class Presence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;
    #[ORM\Column(type: 'string', length: 255)]
    private $prenom;


    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $studPresence;

    #[ORM\Column(type: 'string', length: 255)]
    private $token;


    /**
     * @return mixed
     */
    public function getStudPresence()
    {
        return $this->studPresence;
    }

    /**
     * @param mixed $studPresence
     * @return Presence
     */
    public function setStudPresence($studPresence)
    {
        $this->studPresence = $studPresence;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     * @return Presence
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     * @return Presence
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return Presence
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
