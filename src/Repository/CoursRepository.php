<?php

namespace App\Repository;

use App\Entity\Matiere;
use App\Entity\Formation;
use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

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
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("SELECT 1 FROM cours_formation WHERE cours_id = :cours AND formation_id IN (SELECT formation_id FROM user WHERE id = :user)");
        $resultSet = $stmt->executeQuery(["user" => $userId, "cours" => $coursId]);

        return count($resultSet->fetchAllAssociative()) > 0;
    }

	public function getPresents(int $userId, bool $teacher = false): array
	{
		// Récupération des élèves présents dans la (dernière) salle de cours.
		$conn = $this->getEntityManager()->getConnection();

        if ($teacher)
        {
            // Eleves présents pour les professeurs
            $query = $conn->prepare("SELECT * FROM user WHERE id IN (SELECT user_id FROM `presence_user` WHERE presence_id IN (SELECT presence_id FROM `presence_cours` WHERE cours_id IN (SELECT MAX(cours_id) FROM `cours_user` WHERE `user_id` = :id)));");
            $result = $query->executeQuery(["id" => $userId]);

            return $result->fetchAllAssociative();
        }
        else
        {
            // Présences dans les cours
            $query = $conn->prepare('
                SELECT cours.id, date, type, cours.token, nom_formation, nome_matiere, firsname, lastname FROM cours 
                JOIN cours_formation ON cours.id = cours_formation.cours_id
                JOIN formation ON cours_formation.formation_id = formation.id
                JOIN cours_matiere ON cours.id = cours_matiere.cours_id
                JOIN matiere ON cours_matiere.matiere_id = matiere.id
                JOIN cours_user ON cours.id = cours_user.cours_id
                JOIN user ON cours_user.user_id = user.id
                JOIN presence_user ON presence_user.user_id = :id
                JOIN presence ON presence_user.presence_id = presence.id
                WHERE cours.id IN (SELECT cours_id FROM `presence_cours`)');
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
            $query = $conn->prepare("SELECT * FROM `user` WHERE roles = '[\"ROLE_STUDENT\"]' && id NOT IN (SELECT user_id FROM presence_user WHERE presence_id IN (SELECT presence_id FROM presence_cours WHERE cours_id IN (SELECT MAX(cours_id) FROM cours_user)));");
            $result = $query->executeQuery();

            return $result->fetchAllAssociative();
        }
        else
        {
            // Absences pour les élèves
            $query = $conn->prepare('
                SELECT cours.id, date, type, cours.token, nom_formation, nome_matiere, firsname, lastname FROM cours 
                JOIN cours_formation ON cours.id = cours_formation.cours_id
                JOIN formation ON cours_formation.formation_id = formation.id
                JOIN cours_matiere ON cours.id = cours_matiere.cours_id
                JOIN matiere ON cours_matiere.matiere_id = matiere.id
                JOIN cours_user ON cours.id = cours_user.cours_id
                JOIN user ON cours_user.user_id = user.id
                JOIN presence_user ON presence_user.user_id = :id
                JOIN presence ON presence_user.presence_id = presence.id
                WHERE cours.id NOT IN (SELECT cours_id FROM `presence_cours`)');
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

    public function insertOne(EntityManagerInterface $entityManager, int $userId, string $formation, string $matiere, string $type): int
    {
        // Récupération de la connexion à la base de données
        $conn = $this->getEntityManager()->getConnection();

        // Récupération des fonctions pour les formations, matières et utilisateurs
        $formationRepository = $entityManager->getRepository(Formation::class);
        $matiereRepository = $entityManager->getRepository(Matiere::class);

        // Insertion du cours dans la base de données
        date_default_timezone_set("Europe/Paris");
         
        $insertCours = $conn->prepare("INSERT INTO cours (date, type, terminé, token) VALUES (:date, :type, 0, 1234)");
        $insertCours->executeQuery(["date" => date('Y-m-d H:i:s'), "type" => $type]);

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

		return $coursId;
    }


//    /**
//     * @return Cours[] Returns an array of Cours objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cours
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
