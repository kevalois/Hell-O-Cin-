<?php

namespace App\EventListener;

use App\Entity\Movie;
use App\Utils\Slugger;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class SluggerEntityListener
{
    private $slugger;

    public function __construct(Slugger $slugger)
    {
        $this->slugger = $slugger;
    }

    // On crée une méthode preUpdate
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->prePersist($args);
    }

    // On crée une méthode prePersist
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // Notre slugger doit s'appliquer seulement sur Movie
        // mais par la suite on voudra probablement sluggifier
        // d'autres entités
        if (!$entity instanceof Movie) {
            return;
        }
        // On défini le slut en slugifiant le titre du film
        $entity->setSlug($this->slugger->slugify($entity->getTitle()));
    }
}
