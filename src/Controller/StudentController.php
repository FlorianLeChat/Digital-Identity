<?php

namespace App\Controller;

use App\Repository\PresenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
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
			$code = $userRepository->setPresence($this->getUser(), $cours);
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
    public function presence(PresenceRepository $rp): Response{
        $user = $this->getUser();
        if($user){
            $presences = $rp->findBy(['studPresence'=>$user]);
            return $this->render('student/listePresences.html.twig', compact('presences'));
        }
        else{
            return $this->redirectToRoute('app_login');
        }
    }
}
