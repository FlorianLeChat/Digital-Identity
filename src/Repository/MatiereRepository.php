<?php

namespace App\Repository;

use App\Entity\Matiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matiere>
 *
 * @method Matiere|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matiere|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matiere[]    findAll()
 * @method Matiere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matiere::class);
    }

    public function save(Matiere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Matiere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Permet de récupérer l'identifiant unique d'une matière à partir de son nom
    public function getIdByName(string $nom): string
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM matiere WHERE nome_matiere = :nom";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(["nom" => $nom]);

        return $resultSet->fetch()["id"];
    }

    // Permet de récupérer toutes les matières
    public function getAll(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT nome_matiere FROM matiere WHERE id IN (
            SELECT matiere_id FROM matiere_user WHERE user_id = :id 
            )" ;
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(["id" => $id]);

        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Matiere[] Returns an array of Matiere objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Matiere
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
