<?php

namespace App\Controller\Backend;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * 
 * @Route("/backend/department/", name="backend_department_")
 */
class DepartmentController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(DepartmentRepository $repository)
    {
        $departments = $repository->findAll();

        return $this->render('backend/department/index.html.twig', [
            'departments' => $departments,
        ]);
    }

    /**
     * @Route("{id}", name="show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Department $department = null)
    {
        /*
         Pour pouvoir gerer une erreur custom , il faut prevoir comme valeur par defaut d'entrée
         du null. Ainsi l'objet sera du type l'entité souhaitée OU null.
         De ce fait je peux facilement effectuer un traitement supplementaire (envoyer une erreur custom par ex)
         si je n'ai pas de departement dans mon cas.
        */
        if (!$department) {
            throw $this->createNotFoundException('Département introuvable');
        }
        
        return $this->render('backend/department/show.html.twig', [
            'department' => $department,
        ]);
    }

     /**
     * @Route("{id}/delete", name="delete", methods={"POST","GET"}, requirements={"id"="\d+"})
     */
    public function delete(Department $department, EntityManagerInterface $em)
    {
        $em->remove($department);
        $em->flush();

        $this->addFlash(
            'danger',
            'Suppression effectuée'
        );

        return $this->redirectToRoute('backend_department_index');
    }

    /**
     * Note : il est aussi possibile de mettre 2 routes différente pour une meme fonction
     * cela permet d'avoir des url plus correcte pour la semantique (SEO)
     * 
     * @Route("{id}/editcreate", name="edit_or_create", methods={"GET","POST"}, requirements={"id"="\d+|new"})
     */
    public function editOrCreate($id, Department $department = null, Request $request, EntityManagerInterface $em)
    {   
        //par defaut, les valeurs de flash messages seront prevus pour la modification et seront écrasés si un ajout est détecté à la place
        $flashMessageType = 'info';
        $flashMessage = 'Mise à jour effectuée';

        // GESTION DU COMPORTEMENT SPECIFIQUE EN CAS D'AJOUT
        //si mon departement est null => possibilité que cela soit pour un ajout car id va prendre la valeur "new"
        //note: la valeur new est passé en dur par le lien de création dans la vue twig 
        if(is_null($department)){

            if (!$id) { //si department = null mais qu'id est présent => id n'est pas trouvé en BDD
                throw $this->createNotFoundException('Département introuvable');
            }

            //objet a remplir par handlerequest
            $department = new Department();

            $flashMessageType = 'success';
            $flashMessage = 'Enregistrement effectué';
        }


        $form = $this->createForm(DepartmentType::class, $department);

        //met a jour non seulement la variable formulaire + l'objet associé ($department)
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->persist($department);
            $em->flush();

            $this->addFlash(
                $flashMessageType,
                $flashMessage
            );

            return $this->redirectToRoute('backend_department_index');
        }

        return $this->render('backend/department/edit_or_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
