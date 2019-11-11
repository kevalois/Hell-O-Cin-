<?php

namespace App\Tests\Backend;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    /**
     * URLs accessibles au ROLE_USER en GET
     * 
     * @dataProvider provideRoleUserGetUrls
     */
    public function testUserAccess($url)
    {
        // On se connecte au back
        // cf : https://symfony.com/doc/current/testing/http_authentication.html
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@user.com',
            'PHP_AUTH_PW'   => 'user',
        ]);
        $crawler = $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function provideRoleUserGetUrls()
    {
        return [
            ['/backend/movie/'],
            ['/backend/movie/1'],
            // + les autres entités ...
        ];
    }

    /**
     * URLs accessibles au ROLE_USER en GET
     * 
     * @dataProvider provideRoleUserGetDeniedUrls
     */
    public function testUserAccessDenied($url)
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@user.com',
            'PHP_AUTH_PW'   => 'user',
        ]);
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isForbidden());
        
        $client->request('POST', $url);
        $this->assertTrue($client->getResponse()->isForbidden());
    }

    public function provideRoleUserGetDeniedUrls()
    {
        return [
            ['/backend/movie/1/edit'],
            ['/backend/movie/new'],
            // + les autres entités ...
        ];
    }

}
