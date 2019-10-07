<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\Controller;

use MelisPlatformFrameworkSymfonyDemoToolLogic\Entity\Album;
use MelisPlatformFrameworkSymfonyDemoToolLogic\Form\Type\AlbumType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    /**
     * Get all albums
     *
     * @return Response
     */
    public function getAlbums(): Response
    {
        try {
            /**
             * Get the album list using
             * the album entity
             */
            $album = $this->getDoctrine()
                ->getRepository(Album::class)
                ->findAll();
            $view = $this->render('@MelisPlatformFrameworkSymfonyDemoToolLogic/album.html.twig', ['album_list' => $album])->getContent();
            return new Response($view);
        }catch (\Exception $ex){
            exit($ex->getMessage());
        }
    }

    /**
     * @param $id
     * @return Response
     */
    public function getAlbumById($id)
    {
        try{
            $album = $this->getDoctrine()
                ->getRepository(Album::class)
                ->find($id);

            if (!$album) {
                throw $this->createNotFoundException(
                    'No album found for id '.$id
                );
            }

            $form = $this->createForm(AlbumType::class, $album, [
                'attr' => [
                    'id' => 'album_form'
                ]
            ]);

            return $this->render('@MelisPlatformFrameworkSymfonyDemoToolLogic/form.html.twig', ['form' => $form->createView()]);
        }catch (\Exception $ex){
            exit($ex->getMessage());
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function saveAlbum($id, Request $request): Response
    {exit('x');
        try {
            $entityManager = $this->getDoctrine()->getManager();

            if(empty($id)) {
                $album = new Album();
            }else{
                $album = $entityManager->getRepository(Album::class)->find($id);

                if (!$album) {
                    throw $this->createNotFoundException(
                        'No album found for id '.$id
                    );
                }
            }

            $album->setAlbName($request->get('alb_name'));
            $album->setAlbSongNum($request->get('alb_song_num'));
            // tell Doctrine you want to (eventually) save the Album (no queries yet)
            $entityManager->persist($album);
            // actually executes the queries
            $entityManager->flush();

            return new Response('Album successfully saved');
        }catch (\Exception $ex){
            exit($ex->getMessage());
        }
    }
}