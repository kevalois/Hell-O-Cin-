<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    public function testHomepage()
    {
        // Crée un client (navigateur)
        $client = static::createClient();
        // On éxécute la requête en méthode GET sur l'URL /
        // On récupère un objet $crawler qui va nous permettre de naviguer
        // dans le HTML (DOM) de la réponse reçue
        $crawler = $client->request('GET', '/');

        // On commente cette assertion qui est un peu trop "verbeuse" (elle affiche la réponse)
        //$this->assertResponseIsSuccessful();

        // Vérifions juste que le status est 200 (le debug sera moins violent à l'écran)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Vérifions également qu'on a un contenu spécifique à la page d'accueil
        $this->assertSelectorTextContains('h2#header-margin', 'Retrouvez les films recensés sur Allociné');
    }

    /**
     * Solution 1 pour tester la fiche film
     * suite au souci de fixtures non connues
     */
    public function testMovieShow()
    {
        $client = static::createClient();
        // On va sur la home
        $crawler = $client->request('GET', '/');
        // On cherche le lien du film dans la page, à partir de la sidebar
        // On mémorise le texte du lien (le film cliqué)
        $movieTitle = $crawler->filter('#sidebar a:first-of-type')->text();
        // On chope l'URL du lien
        $link = $crawler->filter('#sidebar a:first-of-type')->link();
        // On stock le titre du lien
        // On demande au client de cliquer sur le lien
        // On récupère le crawler du lien cliqué
        $crawler = $client->click($link);
        // On vérifie qu'on a un statut 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // On vérifie qu'on a le même titre que précédemment
        // Ici, titre de la pasge cliquée
        $newMovieTitle = $crawler->filter('h1#header-margin')->text();
        // On vérifie que les deux titres sont les mêmes
        $this->assertEquals(trim($movieTitle), trim($newMovieTitle));
    }

    /**
     * exemple TDD : on écrit le test avant
     * Testons une page mentions légales
     */
    public function testMentionsLegales()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mentions-legales');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1#header-margin', 'Mentions légales');
    }
}
