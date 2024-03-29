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
use Psr\Container\ContainerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AlbumController extends AbstractController
{
    /**
     * Store all the parameters
     * @var ParameterBagInterface
     */
    protected $parameters;
    protected $container;
    protected $doctrine;

    /**
     * AlbumController constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag, ContainerInterface $container, ManagerRegistry $doctrine)
    {
        $this->parameters = $parameterBag;
        $this->container = $container;
        $this->doctrine = $doctrine;
    }

    /**
     * Function to get the album list
     * and the melis BO languages
     *
     * @return Response
     */
    public function getAlbumTool(): Response
    {
        /**
         * Get the languages on melis back office
         */
        $melisCoreTableLang = $this->melisServiceManager()->getService('MelisCoreTableLang');
        $melisCorelangList = $melisCoreTableLang->fetchAll()->toArray();

        /**
         * Get the album list using
         * the album entity
         */
        $album = $this->doctrine
            ->getRepository(Album::class)
            ->findAll();

        //add the action column
        $view = $this->render('@MelisPlatformFrameworkSymfonyDemoToolLogic/lists.html.twig',
            [
                'album_list' => $album,
                'lang_core_list' => $melisCorelangList,
                "tableConfig" => $this->getAlbumTableConfig(),
                "modalConfig" => $this->getAlbumModalConfig()
            ])->getContent();

        return new Response($view);
    }

    /**
     * Get all album for plugin
     *
     * @return Response
     */
    public function getAlbumPlugin(): Response
    {
        /**
         * Get the album list using
         * the album entity
         */
        $album = $this->doctrine
            ->getRepository(Album::class)
            ->findAll();

        $view = $this->render('@MelisPlatformFrameworkSymfonyDemoToolLogic/album_plugin.html.twig',
            [
                'album_list' => $album
            ])->getContent();

        return new Response($view);
    }

    /**
     * Get album list
     * @param Request $request
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getAlbumList(Request $request)
    {
        /**
         * Prepare the serializer to convert
         * Entity object to array
         */
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
        $colId = array_keys($this->getAlbumTableConfigColumns());
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
        $repository = $this->doctrine->getRepository(Album::class);
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

        //get total filtered record
        $totalFilteredRecord = $serializer->normalize($repository->getTotalFilteredRecord());

        return new JsonResponse(array(
            'draw' => (int) $draw,
            'recordsTotal' => (int) $total,
            'recordsFiltered' => (int) count($totalFilteredRecord),
            'data' => $tableData,
        ));
    }

    /**
     * Create album form
     * @param $id
     * @return Response
     */
    public function createAlbumForm($id)
    {
        $translator = $this->container->get('translator');
        $data = [];
        foreach($this->getAlbumModalConfig()['tabs'] as $tabName => $tab) {
            /**
             * Check if we use form as our content
             */
            if(!empty($tab['form'])) {

                if(!empty($id)) {
                    $album = $this->doctrine
                        ->getRepository(Album::class)
                        ->find($id);

                    if (!$album) {
                        throw $this->createNotFoundException(
                            $translator->trans('tool_no_album_found').' '. $id
                        );
                    }
                }else{
                    $album = new Album();
                }

                $entityName = $tab['form']['entity_class_name'];
                $formTypeName = $tab['form']['form_type_class_name'];
                $formView = $tab['form']['form_view_file'];
                $formId = $tab['form']['form_id'];

                /**
                 * Create form
                 */
                $param['form'] = $this->createForm($formTypeName, $album, [
                    'attr' => [
                        'id' => $formId
                    ]
                ])->createView();

                $data[$tabName] = $this->renderView($formView, $param);
            }else {
                $data[$tabName] = $tab['content'];
            }
        }
        return new JsonResponse($data);
    }

    /**
     * Save album
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAlbum($id, Request $request): JsonResponse
    {
        $itemId = null;
        $result = [
            'title' => 'Album',
            'success' => false,
            'message' => '',
            'errors' => []
        ];

        $translator = $this->container->get('translator');
        if($request->getMethod() == 'POST') {
            $entityManager = $this->doctrine->getManager();
            if (empty($id)) {//create new album
                $album = new Album();
                //set typeCode form logs
                $typeCode = 'SYMFONY_DEMO_TOOL_SAVE';
            } else {//update album
                $album = $entityManager->getRepository(Album::class)->find($id);
                //set typeCode form logs
                $typeCode = 'SYMFONY_DEMO_TOOL_UPDATE';
                if (!$album) {
                    throw $this->createNotFoundException(
                        $translator->trans('tool_no_album_found') .' '. $id
                    );
                }
            }
            $form = $this->createForm(AlbumType::class, $album);
            $form->handleRequest($request);
            //validate form
            if($form->isSubmitted() && $form->isValid()) {
                $album = $form->getData();
                if (empty($id)) {
                $album->setAlbDate(date("Y-m-d H:i:s", time())); 
                }
                // tell Doctrine you want to (eventually) save the Album (no queries yet)
                $entityManager->persist($album);
                // executes the queries
                $entityManager->flush();
                //get id
                $itemId = $album->getAlbId();

                $result['message'] = (empty($id)) ? $translator->trans('tool_album_successfully_saved') : $translator->trans('tool_album_successfully_updated');
                $result['success'] = true;
                //set icon for flash messenger
                $icon = 'glyphicon-info-sign';
            }else{
                $result['message'] = (empty($id)) ? $translator->trans('tool_unable_to_save_album') : $translator->trans('tool_unable_to_update_album');
                $result['errors'] = $this->getErrorsFromForm($form);
                //set icon for flash messenger
                $icon = 'glyphicon-warning-sign';
            }

            //add message notification
            $this->addToFlashMessenger($result['title'], $result['message'], $icon);
            //save logs
            $this->saveLogs($result['title'], $result['message'], $result['success'], $typeCode, $itemId);
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
        $icon = 'glyphicon-warning-sign';
        $typeCode = 'SYMFONY_DEMO_TOOL_DELETE';
        $id = $request->get('id', null);

        $translator = $this->container->get('translator');

        $result = [
            'title' => 'Album',
            'success' => false,
            'message' => $translator->trans('tool_cannot_delete_album'),
        ];
        try {
            $entityManager = $this->doctrine->getManager();
            $album = $entityManager->getRepository(Album::class)->find($id);
            $entityManager->remove($album);
            $entityManager->flush();
            $result['message'] = $translator->trans('tool_album_successfully_deleted');
            $result['success'] = true;
            $icon = 'glyphicon-info-sign';
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }

        //add message notification
        $this->addToFlashMessenger($result['title'], $result['message'], $icon);
        //save logs
        $this->saveLogs($result['title'], $result['message'], $result['success'], $typeCode, $id);

        return new JsonResponse($result);
    }

    /**
     * Get form errors
     * @param FormInterface $form
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form)
    {
        $translator = $this->container->get('translator');
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errMessage = $childErrors[0] ?? null;
                    $fieldLabel = $childForm->getConfig()->getOption('label');
                    $fieldLabel = $translator->trans($fieldLabel);
                    $errors[$childForm->getName()] = ['error_message' => $errMessage, 'label' => $fieldLabel];
                }
            }
        }

        return $errors;
    }

    /**
     * Add logs to notification
     * @param $title
     * @param $message
     * @param string $icon
     */
    private function addToFlashMessenger($title, $message, $icon = 'glyphicon-info-sign')
    {
        $icon = 'glyphicon '.$icon;
        $flashMessenger = $this->melisServiceManager()->getService('MelisCoreFlashMessenger');
        $flashMessenger->addToFlashMessenger($title, $message, $icon);
    }

    /**
     * Save logs
     * @param $title
     * @param $message
     * @param $success
     * @param $typeCode
     * @param $itemId
     */
    private function saveLogs($title, $message, $success, $typeCode, $itemId)
    {
        $logs = $this->melisServiceManager()->getService('MelisCoreLogService');
        $logs->saveLog($title, $message, $success, $typeCode, $itemId);
    }

    /**
     * Get searchable columns
     * @return array|mixed
     */
    private function getAlbumSearchableColumns()
    {
        if(!empty($this->getAlbumTableConfig()['searchables'])){
            return $this->getAlbumTableConfig()['searchables'];
        }
        return [];
    }

    /**
     * Get table columns
     * @return array
     */
    private function getAlbumTableConfigColumns()
    {
        if(!empty($this->getAlbumTableConfig()['columns'])){
            return $this->getAlbumTableConfig()['columns'];
        }
        return [];
    }

    /**
     * Get table config
     * @return mixed|string
     */
    private function getAlbumTableConfig()
    {
        $translator = $this->container->get('translator');
        $tableConfig = [];
        if(!empty($this->parameters->get('symfony_demo_album_table'))){
            $tableConfig = $this->parameters->get('symfony_demo_album_table');
            $tableConfig = $this->translateConfig($tableConfig, $translator);
        }
        return $tableConfig;
    }

    /**
     * Get modal config
     * @return array|mixed
     */
    private function getAlbumModalConfig()
    {
        $translator = $this->container->get('translator');
        $modalConfig = [];
        if(!empty($this->parameters->get('symfony_demo_album_modal'))){
            $modalConfig = $this->parameters->get('symfony_demo_album_modal');
            $modalConfig = $this->translateConfig($modalConfig, $translator);
        }
        return $modalConfig;
    }

    /**
     * Translate some text in the config
     * @param $config
     * @param $translator
     * @return mixed
     */
    private function translateConfig($config, $translator)
    {
        foreach($config as $key => $value){
            if(is_array($value)){
                $config[$key] = $this->translateConfig($value, $translator);
            }else{
                $config[$key] = $translator->trans($value);
            }
        }
        return $config;
    }

    /**
     * Get Melis Service Manager
     * @return object
     */
    private function melisServiceManager()
    {
        return $this->container->get('melis_platform.service_manager');
    }
}