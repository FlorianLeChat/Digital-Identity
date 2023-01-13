<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;


class TeacherController extends AbstractController
{   
    #[Route('/portalTeacher', name: 'app_teacher')]
    public function index(): Response
    {
        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
        ]);
    }

    /**
    * @Route("/generate-code", name="generate_code")
    */
    public function generateCode()
    {
        $code = rand(1000, 9999);
        return $this->render('teacher/code.html.twig', [
            'code' => $code,
        ]);
    }



    #[Route('/portalTeacher', name: 'app_teacher')]
    public function new(Request $request)
{
    $form = $this->createFormBuilder()
        ->add('name', TextType::class)
        ->add('save', SubmitType::class, array('label' => 'Create Task'))
        ->getForm();
    return $this->render('teacher/index.html.twig', array(
        'form' => $form->createView(),
    ));
}

    public function create(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            return $this->redirectToRoute('task_success');
        }
        return $this->render('teacher/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
