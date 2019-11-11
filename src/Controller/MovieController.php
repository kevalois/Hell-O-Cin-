<?php

namespace App\Controller;

use App\Entity\Movie;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CastingRepository;

class MovieController extends AbstractController
{
    /**
     * @Route("/", name="movie_index", methods={"GET","POST"})
     */
    public function index(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);

        $searchTitle = $request->request->get('title');

        if ($searchTitle) {
            $movies = $repository->findByPartialTitle($searchTitle);
        } else {
            //Query builder
            $movies = $repository->findAllQueryBuilderOrderedByName();
        }

        $lastMovies = $repository->lastRelease(10);

        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
            'last_movies' => $lastMovies,
            'searchTitle' => $searchTitle
        ]);
    }

    /**
     * @Route("/movie/{slug}", name="movie_show", methods={"GET"}, requirements={"slug"="[a-z0-9-]+"})
     */
    public function show(Movie $movie, CastingRepository $castingRepository)
    {
        $castings = $castingRepository->findByMovieQueryBuilder($movie);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'castings' => $castings
        ]);
    }

    /**
     * On écrit cette action après avoir écrit le test
     * et on suit les erreurs du test jusqu'à ce que ça passe !
     * 
     * @Route("/mentions-legales", name="app_legal")
     */
    public function legal()
    {
        return $this->render('default/legal.html.twig');
    }
}
