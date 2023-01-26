<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function save(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Permet de récupérer l'identifiant unique à partir du nom de la formation
    public function getIdByName(string $nom): string
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM formation WHERE nom_formation = :nom";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(["nom" => $nom]);

        return $resultSet->fetch()["id"];
    }

    // Permet de récupérer toutes les formations
    public function getAll(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT nom_formation FROM formation";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }
}