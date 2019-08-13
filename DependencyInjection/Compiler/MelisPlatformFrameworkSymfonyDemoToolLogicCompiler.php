<?php
/**
 * Created by PhpStorm.
 * User: cmaka
 * Date: 13/08/2019
 * Time: 5:15 PM
 */

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MelisPlatformFrameworkSymfonyDemoToolLogicCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder)
    {
        if($containerBuilder->has('cvuorinen_example.greeter')){
            $containerBuilder->getDefinition('cvuorinen_example.greeter')->setPublic(true);
        }
//        print_r($containerBuilder->getServiceIds());exit;
//        print_r($containerBuilder->getDefinition('MelisCodeExampleSymfony\TestBundle\Service\TestService'));exit;
    }
}