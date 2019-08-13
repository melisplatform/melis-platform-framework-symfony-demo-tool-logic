<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MelisPlatformFrameworkSymfonyDemoToolLogicController extends AbstractController
{
    public function index()
    {
        try {
            $greeter = $this->get('logic_tool.service');
            return new Response(
                $greeter->greet()
            );
        }catch (\Exception $ex){
            exit($ex->getMessage());
        }
    }
}