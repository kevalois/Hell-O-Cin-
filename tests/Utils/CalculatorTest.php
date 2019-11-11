<?php

namespace App\Tests\Utils;

use App\Utils\Calculator;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire (classe Calculator)
 * on l'éxécute avec `php bin/phpunit` à la racine du projet
 */
class CalculatorTest extends TestCase
{
    public function testAdd()
    {
        // La classe qu'on veut tester
        $calculator = new Calculator();
        // La méthode que l'on souhaite tester
        $result = $calculator->add(30, 12);

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(42, $result);
    }

    /**
     * TDD sur nouvelle fonction
     * on code d'abord le test
     * 
     * Doit retourner le résultat de $a * $b
     */
    public function testMultiply()
    {
        // La classe qu'on veut tester
        $calculator = new Calculator();
        // La méthode que l'on souhaite tester
        $result = $calculator->multiply(10, 3);

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(30, $result);
    }
}
