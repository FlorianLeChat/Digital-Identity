<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

use App\Entity\Formation;
use App\Entity\Matiere;
use App\Entity\Cours;
use Doctrine\ORM\EntityManagerInterface;

class TeacherController extends AbstractController
{
    #[Route('/portalTeacher', name: 'app_teacher')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $id = $this->getUser()->getId();

        $formationRepository = $entityManager->getRepository(Formation::class);
        $noms_formations = $formationRepository->getAll();

        $matiereRepository = $entityManager->getRepository(Matiere::class);
        $noms_matieres = $matiereRepository->getAll($id);

		$coursRepository = $entityManager->getRepository(Cours::class);

        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
            "noms_formations" => $noms_formations,
            "noms_matieres" => $noms_matieres,
			"presents" => $coursRepository->getPresents($id, true),
            "absents" => $coursRepository->getAbsents($id, true)
        ]);
    }

    /**
    * @Route("/closeCours/{uuid}", name="close_cours")
    */
    public function closeCours(EntityManagerInterface $entityManager, string $uuid = ""): Response
    {
		$coursRepository = $entityManager->getRepository(Cours::class);
		$coursId = $coursRepository->findIdByUUID($uuid);

		if ($coursId !== 0) {
			$coursRepository = $entityManager->getRepository(Cours::class);
			$coursRepository->setState($coursId);
		}

        return $this->redirectToRoute('app_teacher');
    }

    /**
    * @Route("/generate_qr_code", name="generate_qr_code")
    */
    public function generateQRCodeAction(Request $request, EntityManagerInterface $entityManager)
    {
        // Identifiant de l'utilisateur
        $user = $this->getUser();
        $id = $user->getId();

        // Fonctions de l'objet "Cours"
        $coursRepository = $entityManager->getRepository(Cours::class);

        // Données POST du formulaire
        $formation = $request->request->get("formation");
        $matiere = $request->request->get("matiere");
        $type = $request->request->get("typeCours");

        // Insertion dans la base de données
		$uuid = $coursRepository->insertOne($entityManager, $id, $formation, $matiere, $type);

		// Préparation à la création de l'image finale.
        $writer = new PngWriter();

        // Génération du QR code.
        $qrCode = QrCode::create('http://127.0.0.1:8000/portalStudent/' . $uuid)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Génération du libellé.
        $label = Label::create('QR code de présence')
            ->setTextColor(new Color(255, 0, 0));

		// Création de l'image finale avec le QR code et le libellé.
        $result = $writer->write($qrCode, null, $label);

        // Validation du résultat de la création de l'image.
        $writer->validateResult($result, 'http://127.0.0.1:8000/portalStudent/' . $uuid);

        return $this->render('teacher/code.html.twig', [
			'uuid' => $uuid,
            'qrCode' => $result->getDataUri()
        ]);
    }
}