<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Endroid\QrCode\QrCode; // or use bacon/qr-code-bundle
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

use App\Entity\Formation;
use App\Entity\Matiere;
use App\Entity\Cours;
use App\Repository\FormationRepository;
use App\Repository\MatiereRepository;
use App\Repository\CoursRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class TeacherController extends AbstractController
{   
    #[Route('/portalTeacher', name: 'app_teacher')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $id = $user->getId();

        $formationRepository = $entityManager->getRepository(Formation::class);
        $noms_formations = $formationRepository->getAll();

        $matiereRepository = $entityManager->getRepository(Matiere::class);
        $noms_matieres = $matiereRepository->getAll($id);   
        
        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
            "test" => $id,
            "noms_formations" => $noms_formations,
            "noms_matieres" => $noms_matieres
        ]);
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
        $coursRepository->insertOne($entityManager, $id, $formation, $matiere, $type);



        
        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create('http://127.0.0.1:8000/portalStudent')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create(__DIR__.'/../../public/assets/images/logo_uca.png')
            ->setResizeToWidth(50);

        // Create generic label
        $label = Label::create('QR code')
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, $logo, $label);

        // Validate the result
        $writer->validateResult($result, 'http://127.0.0.1:8000/portalStudent');
    
        return $this->render('teacher/code.html.twig', [
            'qrCode' => $result->getDataUri()
        ]);
    }
    // public function generateCode()
    // {
    //     $code = rand(1000, 9999);
    //     return $this->render('teacher/code.html.twig', [
    //         'code' => $code,
    //     ]);
    // }

}
