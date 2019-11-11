<?php

namespace App\Utils;

class Slugger
{
    public function slugify(string $stringToConvert)
    {
        // Retourne la chaine modifiée
        return preg_replace( '/[^a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*/', '-', strtolower(trim(strip_tags($stringToConvert))) );
    }
}