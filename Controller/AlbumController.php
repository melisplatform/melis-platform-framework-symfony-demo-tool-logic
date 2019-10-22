<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\Controller;

use MelisPlatformFrameworkSymfonyDemoToolLogic\Entity\Album;
use MelisPlatformFrameworkSymfonyDemoToolLogic\Form\Type\AlbumType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AlbumController extends AbstractController
{
    protected $parameters;

    /**
     * AlbumController constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameters = $parameterBag;
    }

    /**
     * Function to get the album list
     * and the melis BO languages
     *
     * @return Response
     */
    public function getAlbumTool(): Response
    {
        try {
            try {
                /**
                 * Get the Melis Platform services registered in zend
                 * service manager
                 */
                $melisServices = $this->get('melis_platform.service_manager');
                /**
                 * Get the languages on melis back office
                 */
                $melisServices = $melisServices->getService('MelisCoreTableLang');
                $melisCorelangList = $melisServices->fetchAll()->toArray();

                /**
                 * Get the album list using
                 * the album entity
                 */
                $album = $this->getDoctrine()
                    ->getRepository(Album::class)
                    ->findAll();

                /**
                 * get table columns text
                 */
                $columns = [];
                if(!empty($this->getAlbumTableColumns())){
                    foreach($this->getAlbumTableColumns() as $key => $col){
                        $columns[$key] = $col['text'];
                    }
                }
                //add the action column
                $columns['action'] = 'Action';
                $view = $this->render('@MelisPlatformFrameworkSymfonyDemoToolLogic/lists.html.twig',
                    [
                        'album_list' => $album,
                        'lang_core_list' => $melisCorelangList,
                        'tableColumns' => $columns,
                        'getDataTableConfiguration' => $this->symfonyService()->getDataTableConfiguration($this->getAlbumTable())
                    ])->getContent();

                return new Response($view);
            }catch (\Exception $ex){
                exit($ex->getMessage());
            }
        }catch (\Exception $ex){
            exit($ex->getMessage());
        }
    }

    /**
     * Get all album for plugin
     *
     * @return Response
     */
    public function getAlbumPlugin(): Response
    {
        try {
            /**
             * Get the album list using
             * the album entity
             */
            $album = $this->getDoctrine()
                ->getRepository(Album::class)
                ->findAll();

            $view = $this->render('@MelisPlatformFrameworkSymfonyDemoToolLogic/album_plugin.html.twig',
                [
                    'album_list' => $album
                ])->getContent();

            return new Response($view);

        }catch (\Exception $ex){
            exit($ex->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getAlbumList(Request $request)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        /**
         * Prepare all the parameters need
         * for data table
         */
        //get sort order
        $sortOrder = $request->get('order', 'ASC');
        $sortOrder = $sortOrder[0]['dir'];
        //get column name to sort
        $colId = array_keys($this->getAlbumTableColumns());
        $selCol = $request->get('order', 'alb_id');
        $selCol = $colId[$selCol[0]['column']];
        //convert column name(ex. albName) to exact field name in the table(ex. alb_name)
        $selCol = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $selCol)), '_');
        //get draw
        $draw = $request->get('draw', 1);
        //get offset
        $start = (int)$request->get('start', 1);
        //get limit
        $length = (int)$request->get('length', 5);
        //get search value
        $search = $request->get('search', null);
        $search = $search['value'];

        //get repository
        $repository = $this->getDoctrine()->getRepository(Album::class);
        //get total records
        $total = $repository->getTotalRecords();
        //get data
        $tableData = $repository->getAlbum($search, $this->getAlbumSearchableColumns(), $selCol, $sortOrder, $length, $start);
        //convert entity object to array
        $tableData = $serializer->normalize($tableData, null);

        //insert album id to every row
        for ($ctr = 0; $ctr < count($tableData); $ctr++) {
            // add DataTable RowID, this will be added in the <tr> tags in each rows
            $tableData[$ctr]['DT_RowId'] = $tableData[$ctr]['albId'];
        }

        return new JsonResponse(array(
            'draw' => (int) $draw,
            'recordsTotal' => (int) $total,
            'recordsFiltered' => (int) $total,
            'data' => $tableData,
        ));
    }

    /**
     * @param $id
     * @return Response
     */
    public function createAlbumForm($id)
    {
        try{
            /**
             * If id is not empty,
             * then we retrieve the data by id and
             * pass it data to form
             * else just create the form
             */
            if(!empty($id)) {
                $album = $this->getDoctrine()
                    ->getRepository(Album::class)
                    ->find($id);

                if (!$album) {
                    throw $this->createNotFoundException(
                        'No album found for id ' . $id
                    );
                }
            }else{
                $album = new Album();
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
     * Save album
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAlbum($id, Request $request): JsonResponse
    {
        $result = [
            'title' => 'Album',
            'success' => false,
            'message' => '',
            'errors' => []
        ];

        try {
            $translator = $this->get('translator');
            if($request->getMethod() == 'POST') {
                $entityManager = $this->getDoctrine()->getManager();
                if (empty($id)) {//create new album
                    $album = new Album();
                } else {//update album
                    $album = $entityManager->getRepository(Album::class)->find($id);
                    if (!$album) {
                        throw $this->createNotFoundException(
                            $translator->trans('tool.no_album_found') .' '. $id
                        );
                    }
                }
                $form = $this->createForm(AlbumType::class, $album);
                $form->handleRequest($request);
                //validate form
                if($form->isSubmitted() && $form->isValid()) {
                    $album = $form->getData();
                    // tell Doctrine you want to (eventually) save the Album (no queries yet)
                    $entityManager->persist($album);
                    // executes the queries
                    $entityManager->flush();

                    $result['message'] = $translator->trans('tool.album_successfully_saved');
                    $result['success'] = true;
                }else{
                    $result['message'] = $translator->trans('tool.unable_to_save_album');
                    $result['errors'] = $this->getErrorsFromForm($form);
                }
            }
        }catch (\Exception $ex){
            $result['message'] = $ex->getMessage();
        }

        return new JsonResponse($result);
    }

    /**
     * Delete album
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteAlbum(Request $request): JsonResponse
    {
        $translator = $this->get('translator');

        $result = [
            'title' => 'Album',
            'success' => false,
            'message' => $translator->trans('tool.cannot_delete_album'),
        ];
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $album = $entityManager->getRepository(Album::class)->find($request->get('id'));
            $entityManager->remove($album);
            $entityManager->flush();
            $result['message'] = $translator->trans('tool.album_successfully_deleted');
            $result['success'] = true;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
        return new JsonResponse($result);
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    /**
     * @return array|mixed
     */
    private function getAlbumSearchableColumns()
    {
        if(!empty($this->getAlbumTable()['searchables'])){
            return $this->getAlbumTable()['searchables'];
        }
        return [];
    }

    /**
     * @return array
     */
    private function getAlbumTableColumns()
    {
        if(!empty($this->getAlbumTable()['columns'])){
            return $this->getAlbumTable()['columns'];
        }
        return [];
    }

    /**
     * @return mixed|string
     */
    private function getAlbumTable()
    {
        if(!empty($this->parameters->get('symfony_demo_album_table'))){
            return $this->parameters->get('symfony_demo_album_table');
        }
        return '';
    }

    /**
     * This will get the config needed for
     * the datatable using the MelisCoreTool service
     * @param $melisServices
     * @return mixed
     */
    private function getTool($melisServices)
    {
        $tool = $melisServices->getService('MelisCoreTool');
        $tool->setMelisToolKey('melisplatformframeworksymfonydemotool', 'symfony_demo_tool');

        return $tool;
    }

    /**
     * Get service
     * @return object
     */
    private function symfonyService()
    {
        return $this->get('melis_platform_framework.symfony_service');
    }
}