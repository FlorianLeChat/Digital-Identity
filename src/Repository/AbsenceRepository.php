<?php

namespace App\Repository;

use App\Entity\Absence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Absence>
 *
 * @method Absence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Absence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Absence[]    findAll()
 * @method Absence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbsenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Absence::class);
    }

    public function save(Absence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Absence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function insertJustificatif(int $coursId, int $userId): void
    {
        $conn = $this->getEntityManager()->getConnection();

        $insertJustificatif = $conn->prepare("INSERT INTO absence (cours_id, justification_statut) VALUES (:cours, 0)");
        $insertJustificatif->executeQuery(["cours" => $coursId]);

        $insertId = $conn->lastInsertId();

        $insertAbsence = $conn->prepare("INSERT INTO absence_user VALUES (:absence, :user)");
        $insertAbsence->executeQuery(["absence" => $insertId, "user" => $userId]);
    }
}