<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

	public function setPresence(UserInterface $user, int $coursId): int
	{
        // Récupération de la connexion à la base de données
        $conn = $this->getEntityManager()->getConnection();

		// Vérification de l'existence du cours (et si il n'est pas terminé)
        $checkCours = $conn->prepare("SELECT 1 FROM cours WHERE id = :id AND terminé = 0");
        $resultCheckCours = $checkCours->executeQuery(["id" => $coursId]);

		if (!$resultCheckCours->fetch()) {
			// Le cours n'existe pas ou est terminé.
			return 2;
		}

        // Identifiant de l'utilisateur
        $userId = $user->getId();

		// Vérification de si le cours est terminé
        $checkPresent = $conn->prepare("SELECT 1 FROM presence_user WHERE user_id = :userId AND presence_id IN (SELECT presence_id FROM presence WHERE token = :coursId)");
        $resultCheckPresent = $checkPresent->executeQuery(["userId" => $userId, "coursId" => $coursId]);

		if ($resultCheckPresent->fetch()) {
			// L'utilisateur est déjà présent.
			return 1;
		}

		// Mise à jour de la présence.
        // Récupération de l'identifiant unique du cours
        $insertUser = $conn->prepare("INSERT INTO presence (token) VALUES (:token)");
        $insertUser->executeQuery(["token" => $coursId]);

        $presenceId = $conn->lastInsertId();

        $insertFormation = $conn->prepare("INSERT INTO presence_cours VALUES (:presenceId, :coursId)");
        $insertFormation->executeQuery(["presenceId" => $presenceId, "coursId" => $coursId]);

        $insertMatiere = $conn->prepare("INSERT INTO presence_user VALUES (:presenceId, :userId)");
        $insertMatiere->executeQuery(["presenceId" => $presenceId, "userId" => $userId]);

		return 0;
	}

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
