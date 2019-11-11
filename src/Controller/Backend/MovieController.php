<?php

namespace App\Controller\Backend;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Utils\Slugger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/backend/movie", name="backend_")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/", name="movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('backend/movie/index.html.twig', ['movies' => $movieRepository->findAll()]);
    }

    /**
     * @Route("/new", name="movie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //avant l'enregistrement d'un film je dois recuperer l'objet fichier qui n'est pas une chaine de caractere
            $file = $movie->getPoster();

            if(!is_null($movie->getPoster())){

                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec l'extension du fichier d'origine
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                try {

                    //je deplace mon fichier dans le dossier souhaité
                    $file->move(
                        $this->getParameter('poster_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    dump($e);
                }

                $movie->setPoster($fileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($movie);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );
            
            return $this->redirectToRoute('backend_movie_index');
        }

        return $this->render('backend/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="movie_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Movie $movie = null): Response
    {
        if (!$movie) {
            throw $this->createNotFoundException('Film introuvable');
        }

        return $this->render('backend/movie/show.html.twig', ['movie' => $movie]);
    }

    /**
     * @Route("/{id}/edit", name="movie_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Movie $movie): Response
    {
        $oldPoster = $movie->getPoster();

        if(!empty($oldPoster)) {
            $movie->setPoster(
                new File($this->getParameter('poster_directory').'/'.$oldPoster)
            );
        }

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            if(!is_null($movie->getPoster())){

                $file = $movie->getPoster();
            
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('poster_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                
                $movie->setPoster($fileName);

                if(!empty($oldPoster)){

                    unlink(
                        $this->getParameter('poster_directory') .'/'.$oldPoster
                    );
                }

            } else {
                
                $movie->setPoster($oldPoster);//ancien nom de fichier
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            return $this->redirectToRoute('backend_movie_index', ['id' => $movie->getId()]);
        }

        return $this->render('backend/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="movie_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Movie $movie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($movie);
            $entityManager->flush();

            $this->addFlash(
                'danger',
                'Suppression effectuée'
            );
        }

        return $this->redirectToRoute('backend_movie_index');
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
