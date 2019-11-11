<?php

namespace App\Controller\Backend;

use App\Entity\Movie;
use App\Entity\Casting;
use App\Form\CastingType;
use App\Repository\CastingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/casting", name="backend_")
 */
class CastingController extends AbstractController
{
    /**
     * @Route("/movie/{id}", name="casting_index_by_movie", methods={"GET"})
     */
    public function indexByMovie(Movie $movie, CastingRepository $castingRepository): Response
    {
        return $this->render('backend/casting/index.html.twig', [
            // On utilise la requête de jointure pour optimiser
            'castings' => $castingRepository->findByMovieDQL($movie),
            'movie' => $movie,
        ]);
    }

    /**
     * Affiche le formulaire de création d'un casting
     * sur le film donné dans l'URL !
     * 
     * @Route("/new/movie/{id<\d+>}", name="casting_new_by_movie", methods={"GET","POST"})
     */
    public function new(Movie $movie, Request $request): Response
    {
        $casting = new Casting();
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Movie n'étant plus présent dans le formulaire, on l'associe à la main
            $casting->setMovie($movie);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($casting);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );

            // On redirige vers la liste des castings du film
            // en récupérant l'id du film
            return $this->redirectToRoute('backend_casting_index_by_movie', [
                'id' => $movie->getId(),
            ]);
        }

        return $this->render('backend/casting/new.html.twig', [
            'movie' => $movie,
            'casting' => $casting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="casting_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Casting $casting): Response
    {
        return $this->render('backend/casting/show.html.twig', [
            'casting' => $casting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="casting_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Casting $casting): Response
    {
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            // On redirige vers la liste des castings du film
            // en récupérant l'id du film depuis le casting
            return $this->redirectToRoute('backend_casting_index_by_movie', [
                'id' => $casting->getMovie()->getId(),
            ]);
        }

        return $this->render('backend/casting/edit.html.twig', [
            'casting' => $casting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="casting_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Casting $casting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$casting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($casting);
            $entityManager->flush();

            $this->addFlash(
                'danger',
                'Suppression effectuée'
            );
        }

        return $this->redirectToRoute('backend_casting_index_by_movie', [
            'id' => $casting->getMovie()->getId(),
        ]);
    }
}
