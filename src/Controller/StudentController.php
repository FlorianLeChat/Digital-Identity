<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

use App\Entity\User;
use App\Entity\Absence;
use App\Entity\Cours;
use App\Entity\Presence;

use TCPDF;

class StudentController extends AbstractController
{
    /**
	 * @Route("/portalStudent", name="app_student")
     * @Route("/portalStudent/{uuid}", name="app_student")
     */
    public function index(EntityManagerInterface $entityManager, string $uuid = ""): Response
    {
		$userRepository = $entityManager->getRepository(User::class);
		$coursRepository = $entityManager->getRepository(Cours::class);

		$coursId = $coursRepository->findIdByUUID($uuid);

		if ($coursId !== 0) {
			$address = $_SERVER["REMOTE_ADDR"];
			$hostname = gethostbyaddr($address);

			if ($address !== "127.0.0.1" && !str_contains($hostname, "unice.fr"))
			{
				// On vérifie que l'utilisateur est bien sur le réseau universitaire.
				die("Vous n'êtes pas sur le réseau de l'Université Côte d'Azur.");
			}

			$user = $this->getUser();
			$formation = $coursRepository->checkFormation($user->getId(), $coursId);

			if (!$formation)
				die("Vous n'êtes pas inscrit dans cette formation !");

			$code = $userRepository->setPresence($user, $coursId);
		}

        return $this->render("student/index.html.twig", ["uuid" => $uuid, "code" => $code ?? 0]);
    }

    /**
     * @Route ("/download_certificat/{uuid}", name="download_certificat")
     */
	public function downloadCertificat(EntityManagerInterface $entityManager, string $uuid = ""): Response
	{
		$user = $this->getUser();

		$coursRepository = $entityManager->getRepository(Cours::class);
		$userRepository = $entityManager->getRepository(User::class);

		$coursId = $coursRepository->findIdByUUID($uuid);
		$token = $userRepository->getPresenceToken($user->getId(), $coursId);

		if ($coursId !== 0 && $token !== "")
		{
			// Récupération des données du cours.
			$coursId = $coursRepository->find($coursId);

			$date = $coursId->getDate();
			$auteur = $coursId->getUser()->getValues()[0];

			// Génération du PDF.
			// Source : https://github.com/tecnickcom/TCPDF/blob/2fb1c01bc37487d1f94fe1297f8d8ad1b5c290bb/examples/example_002.php
			$pdf = new TCPDF("P", "mm", "A4", true, "UTF-8", false);

			// Définition du titre du document.
			$pdf->SetTitle("Certificat de présence");

			// Suppression des en-têtes et pieds de page.
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);

			// Définition de la police de caractères par défaut.
			$pdf->SetDefaultMonospacedFont("Courier");

			// Définition des marges extérieures.
			$pdf->SetMargins(15, 25, 15);

			// Définition du découpage automatique des mots.
			$pdf->SetAutoPageBreak(true, 25);

			// Définition du facteur de zoom sur les images.
			$pdf->setImageScale(1.25);

			// Définition de la police de caractères utilisée.
			$pdf->SetFont("dejavusans", "", 10);

			// Ajout d'une page.
			$pdf->AddPage();

			// Préparation à la création de l'image finale.
			$writer = new PngWriter();

			// Génération du QR code de vérification.
			// Note : on encode le token en base64 pour éviter les problèmes d'encodage (https://www.php.net/manual/en/function.base64-decode.php#118244)
			$token = base64_encode($userRepository->encryptToken($token));
			$token = str_replace(array('+', '/'), array('-', '_'), $token);
			$token = rtrim($token, '=');

			$qrCode = QrCode::create('http://127.0.0.1:8000/check_certificat/' . $token)
				->setEncoding(new Encoding('UTF-8'))
				->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
				->setSize(300)
				->setMargin(10)
				->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
				->setForegroundColor(new Color(0, 0, 0))
				->setBackgroundColor(new Color(255, 255, 255));

			$result = $writer->write($qrCode);
			$writer->validateResult($result, 'http://127.0.0.1:8000/check_certificat/' . $token);

			// Insertion du texte.
			$text = <<<EOD
			Certificat de présence au cours :

			Prénom : {$user->getFirsname()}
			Nom : {$user->getLastname()}
			Formation : {$coursId->getFormation()->getValues()[0]->getNomFormation()}
			Matière : {$coursId->getMatiere()->getValues()[0]->getNomeMatiere()}
			Type : {$coursId->getType()}
			Date : {$date->format("d/m/Y")}
			Heure : {$date->format("H:i")}
			Enseignant : {$auteur->getFirsname()} {$auteur->getLastname()}
			EOD;

			// Écriture du texte sur le document.
			$pdf->Write(0, $text, "", 0, "C", true, 0, false, false, 0);
			$pdf->writeHTML("<br /><h2>QR Code de contrôle</h2><img src=\"" . $result->getDataUri() . "\" alt=\"QR code\"><br /><a href=\"http://127.0.0.1:8000/check_certificat/{$token}\">URL de vérification</a>", true, false, true, false, "");

			// Fermeture et affichage du document PDF.
			$pdf->Output("certificat.pdf", "I");
		}

		return $this->redirectToRoute('app_student');
	}

    /**
     * @Route ("/check_certificat/{token}", name="check_certificat")
     */
	public function check_certificat(EntityManagerInterface $entityManager, string $token = ""): Response
	{
		if ($token !== "")
		{
			$userRepository = $entityManager->getRepository(User::class);
			$presenceRepository = $entityManager->getRepository(Presence::class);

			$decryptToken = $userRepository->decryptToken(base64_decode(str_replace(array("-", "_"), array("+", "/"), $token)));

            return $this->render("student/checkCertificate.html.twig", [
				"user" => $presenceRepository->findUserByUUID($decryptToken),
				"cours" => $presenceRepository->findCoursByUUID($decryptToken),
				"phrase" => $token,
				"token" => $decryptToken,
			]);
		}

		return $this->redirectToRoute("app_student");
	}

    /**
     * @Route ("/liste_presence", name="student_presence")
     */
    public function presence(EntityManagerInterface $entityManager): Response
	{
        $user = $this->getUser();
		$coursRepository = $entityManager->getRepository(Cours::class);

        if ($user)
		{
			$id = $user->getId();

            return $this->render('student/listePresences.html.twig', [
				"presents" => $coursRepository->getPresents($id, false)
			]);
        }
        else
		{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route ("/liste_absence", name="student_absence")
     */
    public function absence(EntityManagerInterface $entityManager): Response
	{
        $user = $this->getUser();

        if ($user)
		{
			$id = $user->getId();
			$coursRepository = $entityManager->getRepository(Cours::class);

			// Création du dossier des justificatifs.
			$path = "data/justificatifs/$id";

			if (!file_exists($path))
			{
				if (!mkdir($path, 0777, true)) {
					die('Échec lors de la création des dossiers...');
				}
			}

            return $this->render('student/listeAbsences.html.twig', [
				"absents" => $coursRepository->getAbsents($id, false),
				"justificatifs" => array_values(array_diff(scandir("data/justificatifs/$id/"), array('..', '.')))
			]);
        }
        else
		{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route ("/download_justificatif", name="download_justificatif")
     */
    public function justificatif(EntityManagerInterface $entityManager): Response
	{
        $user = $this->getUser();

        if ($user)
		{
			$id = $user->getId();
			$absenceRepository = $entityManager->getRepository(Absence::class);

			// On génère un chemin d'accès pour le justificatif.
			$path = "data/justificatifs/" . $id . "/" . $_POST["coursId"];

			// On récupère les informations du justificatif.
			$file = $_FILES["justif"];

			// On vérifie si un justificatif est déjà présents.
			if (!file_exists($path))
			{
				// On tente de créer un dossier (si possible).
				// En cas d'échec, une erreur est émise.
				if (!mkdir($path, 0777, true)) {
					die('Échec lors de la création des dossiers...');
				}
			}
			else
			{
				// Dans le cas, on affiche une erreur disant qu'un justificatif existe déjà.
				die('Un justificatif existe déjà pour ce cours.');
			}

			// On vérifie si le téléchargement est terminé.
			if ($file["error"] == UPLOAD_ERR_OK)
			{
				// Si c'est le cas, on déplace le fichier temporaire vers son emplacement de stockage.
				$tmp_name = $_FILES["justif"]["tmp_name"];
				$name = basename($_FILES["justif"]["name"]);

				move_uploaded_file($tmp_name, "$path/$name");

				// Insertion des informations dans la base de données.
				$absenceRepository->insertJustificatif($_POST["coursId"], $id);
			}

			// On redirige enfin l'utilisateur vers la liste de ses absences.
            return $this->redirectToRoute('student_absence');
        }
        else
		{
            return $this->redirectToRoute('student_absence');
        }
    }
}
