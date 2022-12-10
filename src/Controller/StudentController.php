<?php

namespace App\Controller;

use App\Repository\PresenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    /**
     * @Route("/portalStudent", name="app_student")
     */
    public function index(): Response
    {
        return $this->render('student/index.html.twig');
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
