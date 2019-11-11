<?php

namespace App\Tests\Utils;

use App\Utils\Slugger;
use PHPUnit\Framework\TestCase;

/**
 * La classe de test doit petre suffixée par "Test"
 * pour être testée par phpUnit
 */
class SluggerTest extends TestCase
{
    /**
     * On crée une méthode qui va tester une fonction en particulier
     * ici la méthode slugify du Slugger
     * 
     * On préfixe le nom de la méthode avec "test"
     */
    public function testSlugify()
    {
        // On instancie le Slugger
        $slugger = new Slugger();
        // On récupère le retour d'éxécution de la méthode
        // à tester
        // A noter : tant qu'à faire, mettre dans la chaine de test
        // tous les caractères qui sont à traiter par slugify()
        $result = $slugger->slugify('Hello World! 2éÉ');
        // On écrit l'assertion qui permet de valider le test
        // Ici la chaine retournée doit être 'hello-world'
        $this->assertEquals('hello-world-2-', $result);
    }
}
