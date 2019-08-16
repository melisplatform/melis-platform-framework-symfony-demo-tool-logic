<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        /**
         * Get the locale used of melis platform
         * to override the default locale of symfony
         */
        $melisService = $this->container->get('melis_platform.services');
        if(!empty($melisService->getMelisLangLocale())){
            $request->setLocale($melisService->getMelisLangLocale());
        }
    }
}