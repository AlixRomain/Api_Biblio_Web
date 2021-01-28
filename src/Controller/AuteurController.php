<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Nationalite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuteurController extends AbstractController
{
    /**
     * @Route("/auteurs", name="auteurs", methods={"GET"})
     */
    //RECUPERATION DE LA LIST DE GENRE GET
    public function listAuteur( EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $auteurs = $entityManager->getRepository(Auteur::class)->findAll();
        //Ici nous demandons à SF de serialiser uniquement le groupe ListSerialiser pour éviter l'erreur circular reference
        //Ainsi pour chaque objet de $auteurs il va sérialiser uniquement les variable associé à ListSerialiser dans l'entity auteur
        //Ainsi que ceux qui sont rattacher a auteurFullList à travers le champ livres dans auteur, soit Editeur et Auteur
        //On évite ainsi d'erreur de "circulat reference"
        //On récupére ainsi toute les infos des livres à travers le auteur sélectrionner
        $resultat = $serializer->serialize(
            $auteurs,
            'json',
            [
                'groups'=>['auteurFullList'],
                //'attributes' => ['id', 'libelle']
            ]
        );

        return new JsonResponse($resultat,
            200,
            //Le troisièeme paramètre représente le header, on peu lui inserer du contenu si on veux comme un message
            [],
            true);
    }
    /**
     * @Route("/auteur/{id}", name="auteur-show", methods={"GET"})
     */
    //RECUPERATION D'UN SEUL GENRE GET
    //ASTUCE DE FOU !! AVEC {id} dans l'url Symfony fait tout seul un findOneById. Il suffit de declarer Gene $auteur dans la funcion
    public function oneAuteur( SerializerInterface $serializer,Auteur $auteur): Response
    {
        $resultat = $serializer->serialize(
            $auteur,
            'json',
            [
                'groups'=>['listAuteurSerialiser'],
                //'attributes' => ['id', 'libelle']
            ]
        );
        //ici la constante HTTP_OK revient à demander le 200 voir classe response
        return new JsonResponse($resultat,
            Response::HTTP_OK,
            [],
            true);
    }
    /**
     * @Route("/auteur/add", name="add_auteur", methods={"POST"})
     */
    //CREATION D'UN GENRE  POST
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator ): Response
    {
        //Je récupère la data reçu en JSON
        $data = $request->getContent();
        //ATTENTION la création d'un auteur nécessite aussi de pouvoir lui associé sa nationalité. On doit donc décoder en array les datas récupérer
        //Pour en extraire la nouvelle nationalité sous forme d'objet, avant de la setter à l'objet auteur désérialiser
        $dataTab = $serializer->decode($data,'json');
        $Nationalite =  $entityManager->getRepository(Nationalite::class)->find($dataTab['relation']['id']);
        //On désérialise le format Json sous la forme d'un objet auteur Puis on luis attribue la relation nationalité
        $auteur = $serializer->deserialize($data, Auteur::class,'json');
        $auteur->setRelation($Nationalite);


        //Je demande à symfony a matcher une error via les annotations sur les champs d'entity @Assert de l'objet auteur
        $errors = $validator->validate($auteur);
        if (0 === count($errors)){
            //Je l'insere en BDD
            $entityManager->persist($auteur);
            $entityManager->flush();
            //On retourne le code 201 HTTP_CREATED  et on choisit d'envoyer le liens ou l'utilisateur poura retrouver se qu'il à insérer en base
            //Avec ca on se rapproche de l'API REST FULL de richardson
            return new JsonResponse(
                "L'auteur à bien été creer",
                Response::HTTP_BAD_REQUEST,
                ["location"=>"api/auteur/".$auteur->getId()],
                true);
            //On peut aussi retourner l'url complet avec :
            // "location"=> $this->generateUrl('auteurs_show',["id"=>$auteur->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        }else{
            //On serialize l'erreur en JSON avant de la retourner
            $errorsJson = $serializer->serialize($errors,'json');
            return new JsonResponse(
                $errorsJson,
                Response::HTTP_BAD_REQUEST,
                [],
                true
            );
        }
    }
    /**
     * @Route("/auteur/update/{id}", name="update_auteur", methods={"PUT"})
     */
    //MODIFICATION D'UN GENRE
    public function edit(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, Auteur $auteur): Response
    {
        //Je récupère la data reçu en JSON
        $data = $request->getContent();
        //ATTENTION la modification d'un auteur nécessite aussi de pouvoir changer sa nationalité. On doit donc décoder en array les datas récupérer
        //Pour en extraire la nouvelle nationalité sous forme d'objet, avant de la setter à l'objet auteur désérialiser
        $dataTab = $serializer->decode($data,'json');
        $newNationalite =  $entityManager->getRepository(Nationalite::class)->find($dataTab['relation']['id']);
        //Solution 1 On à EXIGER un objet dans un autre. Soit nationalite(relation) dans auteurs
        $auteur = $serializer->deserialize($data, Auteur::class,'json',['object_to_populate' =>$auteur]);
        $auteur->setRelation($newNationalite);
        //Solution 2 On a exiger deux objet l'un à côté de l'autre
        //$serializer->denormalize($dataTab['auteur'],Auteur::class,null,['object_to_populate'=>$auteur]);
        //Je l'insere en BDD
        $entityManager->persist($auteur);
        $entityManager->flush();
        //On retourne le code 200
        return new JsonResponse(
            "L' auteur à bien été modifié",
            Response::HTTP_OK,
            [],
            true);
    }
    /**
     * @Route("/auteur/delete/{id}", name="delete_auteur", methods={"DELETE"})
     */
    //MODIFICATION D'UN GENRE
    public function delete( EntityManagerInterface $entityManager,Auteur $auteur): Response
    {
        if($auteur){
            $entityManager->remove($auteur);
            $entityManager->flush();
        }else{
            return new JsonResponse(
            //On retourne le code 404 pour non trouvé
            "L'auteur a bien été supprimé",
                Response::HTTP_NOT_FOUND,
                [],
                false);
        }
        //On retourne le code 200 pour ok
        return new JsonResponse(
            "L'auteur a bien été supprimé",
            Response::HTTP_OK,
            [],
            false);
    }
}
