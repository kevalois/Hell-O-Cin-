<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/backend/user", name="backend_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('backend/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'validation_groups' => ['Default', 'new'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Mot de passe qui vient du formulaire
            $password = $user->getPassword();
            // On l'encode via le passwordEncoder reçu depuis la méthode du contrôleur
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            // On écrase le mot de passe avec le mot de passe encodé
            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('backend_user_index');
        }

        return $this->render('backend/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('backend/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // On stocke le mot de passe courant
        $currentPassword = $user->getPassword();

        $form = $this->createForm(UserType::class, $user, [
            'validation_groups' => ['Default'],
        ]);
        // Ici, le nouveau mot de passe sera renseigné dans $user
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Si le mot de passe n'est pas modifié, on conserve l'ancien
            if (empty($user->getPassword())) {
                $user->setPassword($currentPassword);
            // Sinon, on encode le nouveau mot de passe
            } else {
                // Mot de passe qui vient du formulaire
                $password = $user->getPassword();
                // On l'encode via le passwordEncoder reçu depuis la méthode du contrôleur
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                // On écrase le mot de passe avec le mot de passe encodé
                $user->setPassword($encodedPassword);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_user_index');
        }

        return $this->render('backend/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_user_index');
    }
}
