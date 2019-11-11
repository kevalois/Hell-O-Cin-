<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Ce genre de classe de test permet de vérifier rapidement
 * qu'une grosse partie de l'appli "n'est pas cassée"
 */
class SmokeTest extends WebTestCase
{
    /**
     * Tous les users ont accès à ces URLS (en GET)
     * 
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provideUrls()
    {
        return [
            ['/'],
            ['/mentions-legales'],
            ['/login'],
            ['/movie/a-bug-s-life'],
            // ...
        ];
    }

    /**
     * Vérification des pages bloquées pour l'anonyme en GET

     * @dataProvider provideAnonymousGetUrls
     */
    public function testAnonymousUserAutorizationGet($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        // On attend une redirection
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function provideAnonymousGetUrls()
    {
        return [
            ['/backend/movie/'],
            ['/backend/movie/1'],
            ['/backend/movie/1/edit'],
            ['/backend/movie/new'],
            // + les autres entités ...
        ];
    }

    /**
     * Vérification des pages bloquées pour l'anonyme en POST

     * @dataProvider provideAnonymousPostUrls
     */
    public function testAnonymousUserAutorizationPost($url)
    {
        $client = self::createClient();
        $client->request('POST', $url);

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function provideAnonymousPostUrls()
    {
        return [
            ['/backend/movie/1/edit'],
            ['/backend/movie/new'],
            // ...
        ];
    }

    /**
     * Vérification des pages bloquées pour l'anonyme en DELETE

     * @dataProvider provideAnonymousDeleteUrls
     */
    public function testAnonymousUserAutorizationDelete($url)
    {
        $client = self::createClient();
        $client->request('DELETE', $url);

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function provideAnonymousDeleteUrls()
    {
        return [
            ['/backend/movie/1'],
            // ...
        ];
    }
}
