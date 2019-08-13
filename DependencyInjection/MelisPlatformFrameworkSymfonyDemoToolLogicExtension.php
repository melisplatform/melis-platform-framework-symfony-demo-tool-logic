<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MelisPlatformFrameworkSymfonyDemoToolLogicExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');
//        $container->register('test_service', 'MelisCodeExampleSymfony\TestBundle\Service\TestService');
//        $container->register('test_controller', 'MelisCodeExampleSymfony\TestBundle\Controller\TestController')
//        ->addArgument(new Reference('test_service'));
    }
}