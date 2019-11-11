<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private $isMaintenance;

    // Pour configurer la maintenance depuis le services.yaml
    public function __construct(bool $isMaintenance)
    {
        $this->isMaintenance = $isMaintenance;
    }

    public function onResponseEvent(ResponseEvent $event)
    {
        // Exit for profiler
        if (preg_match('/^\/_profiler/', $event->getRequest()->getPathInfo())) {
            return;
        }

        // Y'a-t-il une maintenance prévue ?
        if (!$this->isMaintenance) {
            return;
        }

        // On récupère la réponse
        $response = $event->getResponse();

        // API / JSON ?
        if ($response->headers->get('content-type') == 'application/json') {
            // Pas de message pour l'API
            return;
        }

        // On récupére le corps de la réponse
        $content = $response->getContent();

        // On ajoute notre HTML de maintenance dans le contenu
        // <3 Eredost
        // 1. On défini le HTML de notre bannière
        // à noter la syntaxe Heredoc
        $maintenanceContent = <<< HTML
<div class="alert alert-danger text-center">Maintenance prévu le 04/10</div>
HTML;
        // 2. On remplace le contenu d'origine en ajout la bannière
        // en s'appuyant sur la chaine de la balise body
        $content = str_replace('<body>', '<body>' . $maintenanceContent, $content);
        
        // 3. On met à jour le contenu de la réponse
        $response->setContent($content);
    }

    public static function getSubscribedEvents()
    {
        return [
            ResponseEvent::class => 'onResponseEvent',
        ];
    }
}
