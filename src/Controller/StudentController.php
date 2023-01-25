<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PresenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Absence;
use App\Entity\Cours;
use TCPDF;

class StudentController extends AbstractController
{
    /**
	 * @Route("/portalStudent", name="app_student")
     * @Route("/portalStudent/{cours}", name="app_student")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, int $cours = 0): Response
    {
		if ($cours !== 0) {
			$userRepository = $entityManager->getRepository(User::class);
			$coursRepository = $entityManager->getRepository(Cours::class);

			$user = $this->getUser();
			$formation = $coursRepository->checkFormation($user->getId(), $cours);

			if (!$formation)
				die("Vous n'êtes pas inscrit dans cette formation !");

			$code = $userRepository->setPresence($user, $cours);
		}

        return $this->render('student/index.html.twig', ['cours' => $cours, 'code' => $code ?? 0]);
    }


    /**
     * @Route ("/download_certificat/{cours}", name="download_certificat")
     */
	public function downloadCertificat(EntityManagerInterface $entityManager, int $cours = 0): Response
	{
		if ($cours !== 0) {
			// Récupération des données du cours.
			$coursRepository = $entityManager->getRepository(Cours::class);
			$cours = $coursRepository->find($cours);

			$date = $cours->getDate();
			$auteur = $cours->getUser()->getValues()[0];

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

			// Insertion du texte.
			$text = <<<EOD
			Certificat de présence au cours :

			Prénom : {$this->getUser()->getFirsname()}
			Nom : {$this->getUser()->getLastname()}
			Formation : {$cours->getFormation()->getValues()[0]->getNomFormation()}
			Matière : {$cours->getMatiere()->getValues()[0]->getNomeMatiere()}
			Type : {$cours->getType()}
			Date : {$date->format("d/m/Y")}
			Heure : {$date->format("H:i")}
			Enseignant : {$auteur->getFirsname()} {$auteur->getLastname()}
			EOD;

			// Écriture du texte sur le document.
			$pdf->Write(0, $text, "", 0, "C", true, 0, false, false, 0);

			// Fermeture et affichage du document PDF.
			$pdf->Output("certificat.pdf", "I");
		}

		return $this->redirectToRoute('app_student');
	}

    /**
     * @Route ("/liste_presence", name="student_presence")
     */
    public function presence(EntityManagerInterface $entityManager): Response
	{
        $user = $this->getUser();
		$coursRepository = $entityManager->getRepository(Cours::class);

        if($user)
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
