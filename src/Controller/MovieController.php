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
     * @Route("/movie/{id}", name="movie_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Movie $movie, CastingRepository $castingRepository)
    {
        $castings = $castingRepository->findByMovieQueryBuilder($movie);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'castings' => $castings
        ]);
    }
}
