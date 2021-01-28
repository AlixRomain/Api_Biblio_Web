<?php

namespace App\Controller;

use App\Repository\AdherentRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * Renvoie le nombre de prêts par adhérent
     * @Route(
     *     path ="/api/adherents/nbPretsParAdherent",
     *     name="adherents_nbPrets",
     *     methods={"GET"}
     * )
     */
    public function nombrePretsParAdherent(AdherentRepository $managerAdherent): Response
    {
        $nbPretParAdherent = $managerAdherent->nbPretsParAdherent();
        return $this->json($nbPretParAdherent);
    }
    /**
     * Renvoie les 5 meilleurs livres
     * @Route(
     *     path ="/api/livres/meilleurslivres",
     *     name="meilleurslivres",
     *     methods={"GET"}
     * )
     */
    public function cinqM(LivreRepository $managerLivre): Response
    {
        $meilleursLivres = $managerLivre->TrouveMelleursLivres();
        return $this->json($meilleursLivres);
    }
}
