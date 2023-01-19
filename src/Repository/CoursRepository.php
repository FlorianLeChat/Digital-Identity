<?php

namespace App\Repository;

use App\Entity\Matiere;
use App\Entity\Formation;
use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

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

	public function getEleves(): array
	{
		// Récupération des élèves présents dans la (dernière) salle de cours.
		$conn = $this->getEntityManager()->getConnection();

		$query = $conn->prepare("SELECT * FROM `presence` WHERE `token` IN (SELECT MAX(cours_id) FROM `cours_user`);");
		$result = $query->executeQuery();

		return $result->fetchAllAssociative();
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
        $insertCours = $conn->prepare("INSERT INTO cours (date, type, terminé) VALUES (:date, :type, 0)");
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