<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\Controller;

use MelisPlatformFrameworkSymfonyDemoToolLogic\Entity\Album;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    /**
     * Function to get the album list
     * and the cms news list using the Melis
     * Platform MelisCmsNews service
     *
     * @return Response
     */
    public function list(): Response
    {
        try {
            /**
             * Get the Melis Platform services registered in zend
             * service manager
             */
            $melisServices = $this->get('melis_platform.service_manager')->getService('MelisCoreTableLang');
            $melisCorelangList = $melisServices->fetchAll()->toArray();

            /**
             * Get the album list using
             * the album entity
             */
            $album = $this->getDoctrine()
                ->getRepository(Album::class)
                ->findAll();

            $view = $this->render('@MelisPlatformFrameworkSymfonyDemoToolLogic/lists.html.twig', ['album_list' => $album, 'lang_core_list' => $melisCorelangList])->getContent();
            return new Response($view);
        }catch (\Exception $ex){
            exit($ex->getMessage());
        }
    }
}