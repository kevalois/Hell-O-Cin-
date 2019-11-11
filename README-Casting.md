# Challenge(s) Movie DB

Au choix tout ou partie des challenges suivants.

## Challenge gestion Casting par film

> Objectif : Pour l'ensemble des films , amélioration de l'accessibilité (actions et consultations) aux données de leur casting respectifs.
Le but étant de modifier le code généré par le CRUD pour qu'il soit un peu plus customisé.

Depuis la page liste de film:

- Rajouter un bouton "Casting" qui permet de consulter la liste de castings du film concerné

Depuis la page liste de castings :

- Le bouton "New" doit amener sur un formulaire sans le film.
- Modifier le code de l'action "New" du contrôleur pour lier le `Casting` au film concerné dorénavant passé par l'URL.
- Vérifier que tout se passe bien pour Casting "Edit"
- Les boutons "Back to list" doivent fonctionner (retour à la liste des casting du film concerné).
- Supprimer la fonctionnalité relative a l'affichage de la totalité des castings (car plus fonctionnelle) et modifier la navigation en conséquence

### Exemples de rendu

1. Movie :

![](https://github.com/O-clock-Alumni/fiches-recap/blob/master/symfony/themes/img/moviescasting.png)

2. Casting :

![](https://github.com/O-clock-Alumni/fiches-recap/blob/master/symfony/themes/img/castingdetail.png)

3. Casting - form:

![](https://github.com/O-clock-Alumni/fiches-recap/blob/master/symfony/themes/img/casting-form.png)

## Challenge - installer et configurer EasyAdminBundle

- Installer le bundle et le configurer pour les entités souhaitées.
- Tentez de pousser au max la configuration spécifique à notre projet (gestion des Casting ou Team par film, voir comment le bundle gère les relations, notamment).

=> [La documentation du bundle](https://symfony.com/doc/master/bundles/EasyAdminBundle/index.html).

---

Bon courage :muscle:
