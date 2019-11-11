# Les tests (automatisés)

> On peut très bien tester son application, à la main en parcourant les différentes pages, les différentes fonctionnalités possibles.

## Pourquoi tester (via un logiciel de test) ?

- Pour s'assurer que le code fonctionne.
- :hand: L'automatisaiton des tests est beaucoup plus rapide qu'à la main sur un grand nombre de tests !
- Permet de tester les cas limites.
- Eviter les effets de bords : qu'une modif impacte une autre partie du code de manière involontaire.
- Plus globalement, contrôler l'apparition de bugs.
- Obtenir un code de qualité = un code fiable => vous garantissez la qualité de votre code, cela évite que le client ou les utilisateurs les nouveaux bugs...
- La non-qualité a un coût (cf site [Test et Recette](http://www.test-recette.fr/generalites/qualite-logicielle/cout-non-qualite.html)).
- [Les freins et difficultés : pourquoi ne pas tester ?](http://www.test-recette.fr/tests-techniques/bases/freins-et-difficultes.html)

## Quoi tester ?

> On va avoir deux types de tests principaux.

- Test unitaire : on teste les classes et leurs méthodes.
- Test fonctionnel : on teste le parcours de l'application front ou back.

## "Combien" tester => Jusqu'où tester ?

- Le test ne vérifie que ce que l'on a mis en place.
- Doit-on tout tester ? Non, tester de 100% de l'appli serait non seulement inutile techniquement (car les tests fonctionnels permettent de déceler déjà des portions de code liées entre elles) et puis ce serait trop coûteux.
- voir [Couverture de test](http://www.test-recette.fr/tests-techniques/deployer-tests-unitaires/quand-et-ou-ecrire-des-tests-unitaires.html) (code coverage)

## Comment tester ?

- Avec un logiciel ou une librairie de test et l'écriture de tests pour notre application.

## Pratique

### Test unitaire

- On teste des classes et leurs méthodes, voir les deux tests sous `tests/Utils`

### Test fonctionnel

- Si vous utilisez une BDD, le fichier d'environnement local `.env.local` n'est pas chargé, mais vous pouvez configurer votre DB de test dans `.env.test` de la même manière
  - :hand: En cas d'erreur PHP à l'éxécution du test, penser à aller lire le contenu du fichier `var/log/test.log` ! Il contiendra le message d'erreur (notamment, ici, le faut que la BDD n'est pas configurée).
- Pour masquer les `deprecations` des test, ajouter `SYMFONY_DEPRECATIONS_HELPER='disabled=1'` à l'environnement de test `.env.test`
- Exemple de TDD : on crée le test de la page _Mentions Légales_ avant de la concevoir, le test échoue, on implémente la page dans le contrôleur jusqu'à ce que le test passe. Double effet kiss-cool, d'une pierre deux coups, tests présents dans l'application à moindre coût ! Revenir sur les tests si on doit les écrire après-coup, relève plus de la torture :wink:
- Les fixtures/jeu de données changeant potententiellement, on ne peut pas s'appuyer dessus aussi facilement.
  - Solution 1 : on navigue sur le site via le client et le crawler => ça peut fait l'affaire mais c'est un peu overkill, à moins d'avoir une autre solution.
  - Solution 2 : on seed le generator cf : https://github.com/fzaninotto/Faker#seeding-the-generator
  - Solution 2bis : on écrit des fixtures à la mano avec "film1, film2" ...
    - => OK mais on ne peut toujours pas tester sur les ids car la purge du fixtures:load ne le permet pas (en standard)
  - **Solution 3 : on drop la database, on la recrée, on applique les migrations et on load fixtures**...
  - Solution n : à voir sur StackOveflow...
- Suite à quoi nous avons pu créer une suite de tests dans `tests/SmokeTest` mais également dans `tests/Backend/MovieControllerTest` afin d'utiliser un user connecté et de vérifier les règles de sécuritté définies dans `security.yaml`.