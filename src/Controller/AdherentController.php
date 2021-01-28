<?php

namespace App\Controller;

use App\Entity\Adherent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdherentController extends AbstractController
{
    /**
     * renvoie le nombre de prets pour un adhérent
     * ATTENTION pour que cette route soit reconnu par Api_platform il faut lui indiquer dans l'entity ADHERENT
     * regarder dans annotations itemOperation => "getNbPrets"
     * @Route(
     *     path= "api/adherent/{id}/pret/count",
     *     name= "adherent_prets_count",
     *     methods= {"GET"},
     *     defaults={
     *          "_controller" = "\app\controller\AdherentController::nombrePrets",
     *          "_api_resource_class" = "App\Entity\Adherent",
     *          "_api_item_operation_name" = "getNbPrets"
     *     }
     * )
     */
    public function nombrePrets(Adherent $data): Response
    {
        $count = $data->getPrets()->count();
        return $this->json([
            "id" => $data->getId(),
            "nombre_prets"=> $count
        ]);
    }
}
