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

class TeacherController extends AbstractController
{   
    #[Route('/portalTeacher', name: 'app_teacher')]
    public function index(): Response
    {
        $user = $this->getUser();

        $formation = new Formation();
        $matiere = new Matiere();

        $noms_formations = $formation->getFormations();
        $noms_matieres = $formation->getMatieres();

        $dump = dump($noms_formations);

       // var_dump($user);
        $id = $user->getId();

        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
            "test" => $id,
            "noms_formations" => $noms_formations,
            "noms_matieres" => $noms_matieres,
            "dump" => $dump
        ]);
    }

    /**
    * @Route("/generate_qr_code", name="generate_qr_code")
    */

    public function generateQRCodeAction()
    {
        
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
