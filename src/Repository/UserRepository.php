<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Ramsey\Uuid\Uuid;

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

	public function createKeys(): void
	{
		// Définition des options de génération de la clé.
		// Sources : https://www.php.net/manual/en/book.openssl.php#91210 et https://www.php.net/manual/en/function.openssl-pkey-new.php#111769
		$options = [
			"config" => "C:/wamp64/bin/php/php8.2.0/extras/ssl/openssl.cnf", // Configuration OpenSSL.
			"default_md" => "sha512", // Méthode de hachage.
			"private_key_bits" => 4096 // Taille de la clé en bits.
		];

		// Génération d'une paire de clés RSA.
		$pair = openssl_pkey_new($options);

		// Extraction de la clé privée.
		$export = openssl_pkey_export($pair, $private_key, null, $options);

		if (!$export) {
			// Si vous rencontrez cette erreur, vérifiez bien que le chemin d'accès au fichier openssl.cnf est correct.
			throw new \Exception("Erreur lors de la génération de la clé privée.");
		}

		// Récupération de la clé publique.
		$public_key = openssl_pkey_get_details($pair);
		$public_key = $public_key["key"];

		// Enregistrement des clés dans des systèmes de fichiers.
		if (!file_exists("data/keys"))
			mkdir("data/keys", 0777, true);

		file_put_contents("data/keys/private_key.pem", $private_key);
		file_put_contents("data/keys/public_key.pem", $public_key);

		// Modification des permissions des fichiers.
		chmod("data/keys/private_key.pem", 0600);
		chmod("data/keys/public_key.pem", 0600);
	}

	public function encryptToken(string $token): string
	{
		// Permet de chiffrer le jeton de l'utilisateur.
		if (!file_exists("data/keys"))
			$this->createKeys();

		// Génération de la signature du texte avec la clé privée.
		// Sources : https://www.php.net/manual/en/function.openssl-sign.php#77571 et https://stackoverflow.com/a/10316343
		// openssl_sign($token, $signature, file_get_contents("data/keys/private_key.pem"), OPENSSL_ALGO_SHA512);

		// Chiffrement du texte en clair avec la clé publique.
		openssl_public_encrypt($token, $encrypted_text, file_get_contents("data/keys/public_key.pem"));

		return $encrypted_text;
	}

	public function decryptToken(string $token): string
	{
		// Permet de déchiffrer le jeton de l'utilisateur.
		if (!file_exists("data/keys"))
			die("Les clés RSA n'ont pas été générées.");

		// Récupération de la signature enregistrée dans la base de données.
		// $signature = $this->getSignature($token);

		// Vérification de la signature du texte avec la clé publique.
		// $statut = openssl_verify($token, $signature, file_get_contents("data/keys/public_key.pem"), OPENSSL_ALGO_SHA512);

		// Déchiffrement du texte chiffré avec la clé privée.
		if (!openssl_private_decrypt($token, $decrypted_text, file_get_contents("data/keys/private_key.pem"))) {
			die(openssl_error_string());
		}

		// Renvoi du texte déchiffré (jeton en clair).
		return $decrypted_text ?? "";
	}

	public function getSignature(string $token): string
	{
		// Récupération de la connexion à la base de données.
		$conn = $this->getEntityManager()->getConnection();

		// Récupération de la signature de vérification du token.
		$getSignature = $conn->prepare("SELECT signature FROM presence WHERE token = :token");
		$resultGetSignature = $getSignature->executeQuery(["token" => $token]);

		$signature = $resultGetSignature->fetch();

		if (!$token) {
			return "";
		}

		return $signature["signature"];
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

		// Vérification de la présence de l'utilisateur dans le cours.
        $checkPresent = $conn->prepare("SELECT 1 FROM presence_user WHERE user_id = :userId AND presence_id IN (SELECT presence_id FROM presence_cours WHERE cours_id = :coursId)");
        $resultCheckPresent = $checkPresent->executeQuery(["userId" => $userId, "coursId" => $coursId]);

		if ($resultCheckPresent->fetch()) {
			// L'utilisateur est déjà présent.
			return 1;
		}

		// Génération d'un identifiant unique universelle.
		$uuid = Uuid::uuid4();

		// Mise à jour de la présence.
        // Récupération de l'identifiant unique du cours
        $insertUser = $conn->prepare("INSERT INTO presence (token) VALUES (:token)");
        $insertUser->executeQuery(["token" => $uuid]);

        $presenceId = $conn->lastInsertId();

        $insertFormation = $conn->prepare("INSERT INTO presence_cours VALUES (:presenceId, :coursId)");
        $insertFormation->executeQuery(["presenceId" => $presenceId, "coursId" => $coursId]);

        $insertMatiere = $conn->prepare("INSERT INTO presence_user VALUES (:presenceId, :userId)");
        $insertMatiere->executeQuery(["presenceId" => $presenceId, "userId" => $userId]);

		return 0;
	}

	public function getPresenceToken(int $userId, int $coursId): string
	{
		// Récupération de la connexion à la base de données.
		$conn = $this->getEntityManager()->getConnection();

		// Récupération du token de présence.
		$getPresenceToken = $conn->prepare("SELECT token from presence WHERE id IN (SELECT presence_id FROM presence_cours WHERE presence_id IN (SELECT presence_id FROM presence_user WHERE user_id = :user) AND cours_id = :cours)");
		$resultGetPresenceToken = $getPresenceToken->executeQuery(["user" => $userId, "cours" => $coursId]);

		$token = $resultGetPresenceToken->fetch();

		if (!$token) {
			return "";
		}

		return $token["token"];
	}
}