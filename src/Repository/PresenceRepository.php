<?php

namespace App\Repository;

use App\Entity\Presence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Presence>
 *
 * @method Presence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presence[]    findAll()
 * @method Presence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presence::class);
    }

    public function save(Presence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Presence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	public function findCoursByUUID(string $uuid): array
	{
		// Permet de récupérer les informations d'un cours grâce à son jeton de présence.
        $conn = $this->getEntityManager()->getConnection();

		// Présences dans les cours (élèves)
		$query = $conn->prepare('
			SELECT cours.id, date, type, cours.token, nom_formation, nome_matiere, firsname, lastname FROM cours
			JOIN cours_formation ON cours.id = cours_formation.cours_id
			JOIN formation ON cours_formation.formation_id = formation.id
			JOIN cours_matiere ON cours.id = cours_matiere.cours_id
			JOIN matiere ON cours_matiere.matiere_id = matiere.id
			JOIN cours_user ON cours.id = cours_user.cours_id
			JOIN user ON cours_user.user_id = user.id
			JOIN presence ON :token = presence.token
			WHERE cours.id IN (SELECT cours_id FROM `presence_cours`)');
		$result = $query->executeQuery(["token" => $uuid]);
		$result = $result->fetchAssociative();

        return is_array($result) ? $result : [];
	}

	public function findUserByUUID(string $uuid): array
	{
		// Permet de récupérer les informations d'un utilisateur grâce à son jeton de présence.
        $conn = $this->getEntityManager()->getConnection();

        $query = $conn->prepare("SELECT * FROM `user` WHERE id IN (SELECT user_id FROM `presence_user` WHERE `presence_id` IN (SELECT `id` FROM `presence` WHERE `token` = :token))");
        $result = $query->executeQuery(["token" => $uuid]);
		$result = $result->fetchAssociative();

        return is_array($result) ? $result : [];
	}

}