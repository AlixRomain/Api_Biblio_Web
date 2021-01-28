<?php

namespace App\Controller;

use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GenreController extends AbstractController
{
    /**
     * @Route("/genres", name="genres", methods={"GET"})
     */
    //RECUPERATION DE LA LIST DE GENRE GET
    public function listGenre( EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $genres = $entityManager->getRepository(Genre::class)->findAll();
        //Ici nous demandons à SF de serialiser uniquement le groupe ListSerialiser pour éviter l'erreur circular reference
        //Ainsi pour chaque objet de $genres il va sérialiser uniquement les variable associé à ListSerialiser dans l'entity genre
        //Ainsi que ceux qui sont rattacher a genreFullList à travers le champ livres dans genre, soit Editeur et Auteur
        //On évite ainsi d'erreur de "circulat reference"
        //On récupére ainsi toute les infos des livres à travers le genre sélectrionner
        $resultat = $serializer->serialize(
            $genres,
            'json',
            [
                'groups'=>['genreFullList'],
                //'attributes' => ['id', 'libelle']
            ]
        );

        return new JsonResponse($resultat,
            200,
            //Le troisièeme paramètre représente le header, on peu lui inserer du contenu si on veux
            [],
            true);
    }
    /**
     * @Route("/genre/{id}", name="genre-show", methods={"GET"})
     */
    //RECUPERATION D'UN SEUL GENRE GET
    //ASTUCE DE FOU !! AVEC {id} dans l'url Symfony fait tout seul un findOneById. Il suffit de declarer Gene $genre dans la funcion
    public function oneGenre( SerializerInterface $serializer,Genre $genre): Response
    {
        $resultat = $serializer->serialize(
            $genre,
            'json',
            [
                'groups'=>['listSerialiser'],
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
     * @Route("/genre/add", name="add_genre", methods={"POST"})
     */
    //CREATION D'UN GENRE  POST
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator ): Response
    {
        //Je récupère la data reçu en JSON
        $data = $request->getContent();

        //Je la transforme en objet en vue de l'inscrire en BDD
        $genre = $serializer->deserialize($data, Genre::class,'json');
        //Je demande à symfony a matcher une error via les annotations sur les champs d'entity @Assert de l'objet genre
        $errors = $validator->validate($genre);
        if (0 === count($errors)){
            //Je l'insere en BDD
            $entityManager->persist($genre);
            $entityManager->flush();
            //On retourne le code 201 HTTP_CREATED  et on choisit d'envoyer le liens ou l'utilisateur poura retrouver se qu'il à insérer en base
            //Avec ca on se rapproche de l'API REST FULL de richardson
            return new JsonResponse(
                "Le genre à bien été creer",
                Response::HTTP_BAD_REQUEST,
                ["location"=>"api/genre/".$genre->getId()],
                true);
            //On peut aussi retourner l'url complet avec :
            // "location"=> $this->generateUrl('genres_show',["id"=>$genre->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
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
     * @Route("/genre/update/{id}", name="update_genre", methods={"PUT"})
     */
    //MODIFICATION D'UN GENRE
    public function edit(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, Genre $genre): Response
    {
        //Je récupère la data reçu en JSON
        $data = $request->getContent();
        //Ici je dis prend la DaTA , hydrate un objet genre à partir de celui trouver en BDD et ecrase aves les nouvelle
        // DATA ce qui diffère de l'ancien.
        $genre = $serializer->deserialize($data, Genre::class,'json',['object_to_populate' =>$genre]);
        //Je l'insere en BDD
        $entityManager->persist($genre);
        $entityManager->flush();
        //On retourne le code 200
        return new JsonResponse(
            "Le genre à bien été modifié",
            Response::HTTP_OK,
            [],
            true);
    }
    /**
     * @Route("/genre/delete/{id}", name="delete_genre", methods={"DELETE"})
     */
    //MODIFICATION D'UN GENRE
    public function delete( EntityManagerInterface $entityManager,Genre $genre): Response
    {
        if($genre){
            $entityManager->remove($genre);
            $entityManager->flush();
        }else{
            return new JsonResponse(
            //On retourne le code 404 pour non trouvé
            "Le genre a bien été supprimé",
                Response::HTTP_NOT_FOUND,
                [],
                false);
        }
        //On retourne le code 200 pour ok
        return new JsonResponse(
            "Le genre a bien été supprimé",
            Response::HTTP_OK,
            [],
            false);
    }
}
