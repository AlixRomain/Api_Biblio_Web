<?php

namespace App\Controller\Admin;

use App\Entity\Auteur;
use App\Entity\Editeur;
use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\Nationalite;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // Fonction pour te rerouter directement à la connexion sur le controller de son choix
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(LivreCrudController::class)->generateUrl());
        // Pour arriver sur la page initial du dashboard ->return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('La biblio-Web');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Livres', 'fas fa-user', Livre::class);
        yield MenuItem::linkToCrud('Genres', 'fas fa-list', Genre::class);
        yield MenuItem::linkToCrud('Auteurs', 'fas fa-tag', Auteur::class);
        yield MenuItem::linkToCrud('Editeurs', 'fas fa-truck', Editeur::class);
        yield MenuItem::linkToCrud('Nationalité', 'fas fa-shopping-cart', Nationalite::class);
       // yield MenuItem::linkToCrud('Headers', 'fas fa-desktop', Header::class);
    }
}
