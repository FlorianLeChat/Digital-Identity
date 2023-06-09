<?php

namespace App\Repository;

use App\Entity\Matiere;
use App\Entity\Formation;
use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends ServiceEntityRepository<Cours>
 *
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    public function save(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkFormation(int $userId, int $coursId): bool
    {
		// Vérifie si l'utilisateur appartient à la formation du cours.
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("SELECT 1 FROM cours_formation WHERE cours_id = :cours AND formation_id IN (SELECT formation_id FROM user WHERE id = :user)");
        $resultSet = $stmt->executeQuery(["user" => $userId, "cours" => $coursId]);

        return count($resultSet->fetchAllAssociative()) > 0;
    }

	public function findIdByUUID(string $uuid): int
	{
		// Permet de récupérer l'identifiant de la base de données à partir de l'identifiant unique universelle.
        $conn = $this->getEntityManager()->getConnection();

        $query = $conn->prepare("SELECT id FROM cours WHERE token = :token");
        $result = $query->executeQuery(["token" => $uuid]);
		$result = $result->fetchAssociative();

        return is_array($result) ? $result["id"] : 0;
	}

	public function setAbsents(int $userId, int $coursId): void
	{
		// Permet de définir les élèves absents à un cours.
		$conn = $this->getEntityManager()->getConnection();
		$absents = $this->getAbsents($userId, true);

		foreach ($absents as $absent)
		{
			// Pour chaque absent, on insère ses informations dans la base de données.
			$query = $conn->prepare("INSERT INTO absence (justification_statut) VALUES (0)");
			$query->execute();

			$absentId = $conn->lastInsertId();

			$query = $conn->prepare("INSERT INTO absence_cours (absence_id, cours_id) VALUES (:absence, :cours)");
			$query->execute(["absence" => $absentId, "cours" => $coursId]);

			$query = $conn->prepare("INSERT INTO absence_user (absence_id, user_id) VALUES (:absence, :user)");
			$query->execute(["absence" => $absentId, "user" => $absent["id"]]);
		}
	}

	public function getPresents(int $userId, bool $teacher = false): array
	{
		// Récupération des élèves présents dans la (dernière) salle de cours.
		$conn = $this->getEntityManager()->getConnection();

        if ($teacher)
        {
            // Élèves présents pour les professeurs
            $query = $conn->prepare("SELECT * FROM user
            JOIN presence_user ON user.id = presence_user.user_id
            JOIN presence_cours ON presence_user.presence_id = presence_cours.presence_id
            WHERE cours_id IN (SELECT MAX(cours_id) FROM `cours_user` WHERE `user_id` = :id);");
            $result = $query->executeQuery(["id" => $userId]);

            return $result->fetchAllAssociative();
        }
        else
        {
            // Présences dans les cours (élèves)
            $query = $conn->prepare('
				SELECT cours.id, date, type, cours.token, nom_formation, nome_matiere, firsname, lastname FROM cours
				JOIN cours_formation ON cours.id = cours_formation.cours_id
				JOIN formation ON cours_formation.formation_id = formation.id
				JOIN cours_matiere ON cours.id = cours_matiere.cours_id
				JOIN matiere ON cours_matiere.matiere_id = matiere.id
				JOIN cours_user ON cours.id = cours_user.cours_id
				JOIN user ON cours_user.user_id = user.id
				WHERE cours.id IN (SELECT cours_id FROM `presence_cours` WHERE presence_id IN (SELECT presence_id FROM `presence_user` WHERE `user_id` = :id))'
			);
            $result = $query->executeQuery(["id" => $userId]);

            return $result->fetchAllAssociative();
        }
	}

	public function getAbsents(int $userId, bool $teacher = false): array
	{
		// Récupération des élèves présents dans la (dernière) salle de cours.
		$conn = $this->getEntityManager()->getConnection();

        if ($teacher)
        {
            // Absences pour les professeurs
            $cm = $conn->prepare("SELECT type FROM cours WHERE id IN (SELECT MAX(cours_id) FROM cours_user WHERE user_id = :id)");
            $queryCm = $cm->executeQuery(["id" => $userId]);
            $resultCm = $queryCm->fetch();

            $isCM = is_array($resultCm) ? $resultCm["type"] === "CM" : false;

            if ($isCM)
            {
                $query = $conn->prepare("SELECT *
                FROM `user`
                WHERE roles = '[\"ROLE_STUDENT\"]'
                AND formation_id IN (
                  SELECT formation_id
                  FROM cours_formation
                  WHERE cours_id = (
                    SELECT MAX(cours_id)
                    FROM `cours_user`
                    WHERE `user_id` = :id
                  )
                )
                AND id NOT IN (
                  SELECT user_id
                  FROM presence_user
                  WHERE presence_id IN (
                    SELECT presence_id
                    FROM presence_cours
                    WHERE cours_id = (
                      SELECT MAX(cours_id)
                      FROM `cours_user`
                      WHERE `user_id` = :id
                    )
                  )
                );");
                $result = $query->executeQuery(["id" => $userId]);

                return $result->fetchAllAssociative();
            }
            else
            {
                $query = $conn->prepare("SELECT *
                FROM `user`
                WHERE roles = '[\"ROLE_STUDENT\"]'
                AND formation_id IN (
                  SELECT formation_id
                  FROM cours_formation
                  WHERE cours_id = (
                    SELECT MAX(cours_id)
                    FROM `cours_user`
                    WHERE `user_id` = :id
                  )
                )
                AND id NOT IN (
                  SELECT user_id
                  FROM presence_user
                  WHERE presence_id IN (
                    SELECT presence_id
                    FROM presence_cours
                    WHERE cours_id = (
                      SELECT MAX(cours_id)
                      FROM `cours_user`
                      WHERE `user_id` = :id
                    )
                  )
                )
                AND (td IN(
                    SELECT groupe
                    FROM cours
                    WHERE id = (
                      SELECT MAX(cours_id)
                      FROM `cours_user`
                      WHERE `user_id` = :id
                    )
                ) OR tp IN(
                    SELECT groupe
                    FROM cours
                    WHERE id = (
                      SELECT MAX(cours_id)
                      FROM `cours_user`
                      WHERE `user_id` = :id
                    )
                ));");
                $result = $query->executeQuery(["id" => $userId]);

                return $result->fetchAllAssociative();
            }
        }
        else
        {
            // Absences pour les élèves
            $query = $conn->prepare('
                SELECT cours.id, date, type, cours.token, cours_formation.formation_id, groupe, nom_formation, nome_matiere, firsname, lastname FROM cours
                JOIN cours_formation ON cours.id = cours_formation.cours_id
                JOIN formation ON cours_formation.formation_id = formation.id
                JOIN cours_matiere ON cours.id = cours_matiere.cours_id
                JOIN matiere ON cours_matiere.matiere_id = matiere.id
                JOIN cours_user ON cours.id = cours_user.cours_id
                JOIN user ON cours_user.user_id = user.id
                WHERE cours_formation.formation_id IN (SELECT formation_id FROM user WHERE id = :id) AND cours.id NOT IN (SELECT cours_id FROM `presence_cours` WHERE presence_id IN (SELECT presence_id FROM `presence_user` WHERE `user_id` = :id)) AND (cours.groupe IN (SELECT tp FROM user WHERE id = :id) OR cours.groupe IN (SELECT td FROM user WHERE id = :id) OR cours.groupe IS NULL)'
			);
            $result = $query->executeQuery(["id" => $userId]);

            return $result->fetchAllAssociative();
        }
	}

	public function setState(int $coursId): void
	{
		// On met fin à la session de cours.
        $conn = $this->getEntityManager()->getConnection();

		$query = $conn->prepare("UPDATE `cours` SET `terminé` = '1' WHERE `id` = :id");
        $query->executeQuery(["id" => $coursId]);
	}

    public function insertOne(EntityManagerInterface $entityManager, int $userId, string $formation, string $matiere, string $type, ?int $groupe): string
    {
        // Récupération de la connexion à la base de données
        $conn = $this->getEntityManager()->getConnection();
		$uuid = Uuid::uuid4();

        // Récupération des fonctions pour les formations, matières et utilisateurs
        $formationRepository = $entityManager->getRepository(Formation::class);
        $matiereRepository = $entityManager->getRepository(Matiere::class);

        // Insertion du cours dans la base de données
        date_default_timezone_set("Europe/Paris");

        $insertCours = $conn->prepare("INSERT INTO cours (date, type, terminé, token, groupe) VALUES (:date, :type, 0, :uuid, :groupe)");
        $insertCours->executeQuery(["date" => date('Y-m-d H:i:s'), "type" => $type, "uuid" => $uuid->toString(), "groupe" => $groupe]);

        // Récupération de l'identifiant unique du cours
        $coursId = $conn->lastInsertId();

        // Insertion de la relation de l'auteur (professeur) lié au cours
        $insertUser = $conn->prepare("INSERT INTO cours_user VALUES (:cours, :user)");
        $insertUser->executeQuery(["cours" => $coursId, "user" => $userId]);

        // Insertion de la relation de la formation liée au cours
        $insertFormation = $conn->prepare("INSERT INTO cours_formation VALUES (:cours, :formation)");
        $insertFormation->executeQuery(["cours" => $coursId, "formation" => $formationRepository->getIdByName($formation)]);

        // Insertion de la relation de la matière liée au cours
        $insertMatiere = $conn->prepare("INSERT INTO cours_matiere VALUES (:cours, :matiere)");
        $insertMatiere->executeQuery(["cours" => $coursId, "matiere" => $matiereRepository->getIdByName($matiere)]);

		return $uuid;
    }
}