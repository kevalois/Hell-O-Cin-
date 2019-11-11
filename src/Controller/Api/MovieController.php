<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Repository\CastingRepository;
use App\Repository\MovieRepository;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MovieController extends AbstractController
{
    /**
     * @Route("/api/movies", name="api_movies_get", methods={"GET"})
     */
    public function getAll(MovieRepository $movieRepository, SerializerInterface $serializer)
    {
        // On récupère les ressources (movies)
        $movies = $movieRepository->findAllQueryBuilderOrderedByName();
        
        // avec $this->json() souci de référence circulaire donc on fait autrement
        // cf : https://symfony.com/doc/current/serializer.html#using-serialization-groups-annotations
        $jsonData = $serializer->serialize($movies, 'json', [
            'groups' => 'movies_get',
        ]);

        // On les retourne en JSON
        // cf : https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        return JsonResponse::fromJsonString($jsonData);
    }

    /**
     * @Route("/api/movies/{id}", name="api_movies_get_one", methods={"GET"})
     */
    public function getOne($id, Movie $movie, MovieRepository $movieRepository, CastingRepository $castingRepository)
    {
        /*
         * Si on se passe Serializer, on peut faire des requêtes custom
         * qui vont chercher les infos nécessaires sous forme de tableau
         * ... mais c'est pas forcément plus simple !
         */

        // Les castings
        $castings = $castingRepository->findByMovieDQLForSerializing($movie);
        // Le film
        $movie = $movieRepository->findForSerializing($id);
        // On ajoute les castings au film
        $movie['castings'] = $castings;
        dump($movie);
        // Encodage automatique du tableau reçu depuis la requête custom
        // /!\ Tableau et non entité car on a sélectionné que certains champs /!\
        return $this->json($movie);
    }

    /**
     * @Route("/api/movies", name="api_movies_post", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, Slugger $slugger, ValidatorInterface $validator)
    {
        // On récupère le contenu JSON envoyé par le client
        $jsonContent = $request->getContent();

        // On déserialise le JSON vers une entité Doctrine
        $movie = $serializer->deserialize($jsonContent, Movie::class, 'json');
        // dump($movie);

        // On oublie pas le slug
        $movie->setslug($slugger->slugify($movie->getTitle()));

        // On valide l'entité
        $errors = $validator->validate($movie);

        // En cas d'erreurs
        if (count($errors) > 0) {
            // On reconstitue un tableau à partir des erreurs
            // pour informer le front
            $jsonErrors = [];
            foreach ($errors as $error) {
                // Propriété de l'entité en erreur + message d'erreur
                $jsonErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            
            return $this->json($jsonErrors, 422);
        }

        // On sauve en BDD
        $entityManager->persist($movie);
        $entityManager->flush();

        // On doit (convention REST) renvoyer une réponse de redirection
        // vers la ressource créée avec un statut 201
        return new RedirectResponse($this->generateUrl('api_movies_get_one', ['id' => $movie->getId()]), 201);
    }

    /**
     * @Route("/api/movies/test/{id}", name="api_movies_get_one_test", methods={"GET"})
     */
    public function getOneTest(Movie $movie, SerializerInterface $serializer)
    {
        // Ne marche pas :) mais y'a moyen de fouiller de ce côté...
        // cf : https://symfony.com/doc/current/components/serializer.html#handling-circular-references
        $movieJson = $serializer->serialize($movie, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return JsonResponse::fromJsonString($movieJson);
    }
}
