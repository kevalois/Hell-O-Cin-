<?php

namespace App\EventSubscriber;

use Twig\Environment as Twig;
use App\Controller\MovieController;
use App\Repository\MovieRepository;
use Doctrine\Common\Annotations\Annotation\Required;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RandomMovieSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $movieRepository;

    public function onControllerEvent(ControllerEvent $event)
    {
        // 1. Dans quel contrôleur se trouve-ton ?
        // On récupère le contrôleur fourni par l'event
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        // 2. On souhaite récupérer notre randomMovie uniquement depuis MovieController du front
        if (!($controller instanceof MovieController)) {
            return;
        }

        // On va chercher un film au hasard (via Doctrine)
        $movie = $this->movieRepository->getRandomMovie();
        // On le transmet à Twig
        // => la variable Twig sera accessible dans tous les templates
        // https://twig.symfony.com/doc/2.x/advanced.html#globals
        $this->twig->addGlobal('randomMovie', $movie);
    }

    public function setTwigEnvironment(Twig $twig)
    {
        // dump('init Twig');
        $this->twig = $twig;
    }
    
    public function setMovieRepository(MovieRepository $movieRepository)
    {
        // dump('init MovieRepository');
        $this->movieRepository = $movieRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
