<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('country')
            ->add('roles', ChoiceType::class,
                array(
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => array(
                        'Administrateur' => 'ROLE_ADMIN',
                        'Utilisateur' => 'ROLE_USER',
                    )
                ))
            // On crée un écouteur d'événement avant que l'entité
            // pré-remplisse le form
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
        ;
    }

    /**
     * Notre écouteur, qui reçoit l'évenement qui contient les infos nécessaires
     */
    public function onPreSetData(FormEvent $event)
    {
        // On récupère notre entite User depuis l'event
        $user = $event->getData();
        //dump($user);
        // On récupère le form depuis l'event (pour éventuellement le manipuler)
        $form = $event->getForm();

        // Le user est-il nouveau ? (id non défini)
        if ($user->getId() === null) {
            // Si oui => password = obligatoire
            $form->add('password', null, [
                // Permet de définir la valeur du champ
                // si la valaur reçue de la requête est vide
                // https://symfony.com/doc/current/reference/forms/types/password.html#empty-data
                // PS : on pourrait également définir le setter $user->setPassword()
                // avec ?string pour autoriser le null
                'empty_data' => '',
            ]);
        } else {
            // On vide le champ du form pour masquer le password
            // Note : ceci était précédemment dans le contrôleur
            $user->setPassword('');
            // Si non => placeholder à ajouter
            $form->add('password', null, [
                'attr' => [
                    'placeholder' => 'Laissez vide si inchangé',
                ],
                'empty_data' => '',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
