<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Cours;
use App\Entity\Formation;
use App\Entity\Matiere;
use App\Entity\Presence;
use App\Entity\Absence;


use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Application de gestion de présence UCA');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Cours', 'fas fa-book-open', Cours::class);
        yield MenuItem::linkToCrud('Formations', 'fa fa-graduation-cap', Formation::class);
        yield MenuItem::linkToCrud('Matières', 'fas fa-chalkboard-teacher', Matiere::class);
        yield MenuItem::linkToCrud('Présences', 'fas fa-user', Presence::class);
        yield MenuItem::linkToCrud('Absences', 'fas fa-user-times', Absence::class);
        
    }
}