# 1. Challenge User login

## Création du form de login + redirection si login ok

> Tout (ou presque) est pris en charge par le bundle de sécurité.

Voir la doc ici : http://symfony.com/doc/current/security/form_login_setup.html

### Logout

Voir la doc (en bas de page "Logging Out") : http://symfony.com/doc/current/security.html#logging-out

## Bonus : CRUD

Créer le CRUD avec la ldc `php bin/console make:crud`, sur l'entité User.

_Si vous le faites depuis EasyAdminBundle, il faudra trouver dans la doc du bundle comment ajouter ce comportement d'encodage sur un mot de passe_.

### Bonus : Encodage du mot de passe à la sauvegarde de l'utilisateur

Depuis votre interface CRUD :

- à l'ajout d'un utilisateur
- à l'édition d'un utilisateur

Vous pouvez utiliser l'injection de dépendances depuis votre contrôleur, et comme utilisé dans nos fixtures, par ex. : 

```php
// src/Controller/Backend/UserController.php

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// ...

    public function new(UserPasswordEncoderInterface $passwordEncoder)
    {
        // ...

        $encodedPassword = $passwordEncoder->encodePassword($user, 'le mot de passe qui vient du formulaire');
        $user->setPassword($encodedPassword);

        // ...
    }

```

# 2. Challenge Utilisation des Rôles

## Restrictions d'accès

> Configurez l'`access_control` du `security.yaml` afin de restreindre l'accès aux URLs suivantes :

- Si user **ANONYME** : homepage + fiche film seulement.
- Si **ROLE_USER** : accès aux pages de **listes** movie, genres etc. (les liens du menu) et pages **show**.
- Sécuriser toutes les routes /**new** /**edit** /**delete** avec **ROLE_ADMIN**.

## Bonus : Front, inté, Twig

- **Trouver un moyen d'attribuer un rôle à l'utilisateur** depuis l'interface d'admin (champ texte libre dans un champ de User, menu déroulant avec les choix, table liée avec les droits dedans, au choix). **Droit minimal** obligatoire 'ROLE_USER'. N'oubliez pas que User->getRoles() retourne un **tableau** contenant le ou les droits du user. On se contentera d'un seul droit ici.
- **Dans le menu** :
    - **Afficher une info** dans le menu contenant :
        - username
        - role = Visiteur, Membre ou Administrateur
    - **Afficher un bouton** _Connexion_ ou _Déconnexion_ en fonction de l'état de connexion du User.

## Support

[Fiche récap' sur les  Rôles](https://github.com/O-clock-Alumnis/fiches-recap/blob/master/symfony/themes/roles.md)