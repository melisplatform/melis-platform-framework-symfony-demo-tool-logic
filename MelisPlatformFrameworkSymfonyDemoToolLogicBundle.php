<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic;

use MelisPlatformFrameworkSymfonyDemoToolLogic\DependencyInjection\Compiler\MelisPlatformFrameworkSymfonyDemoToolLogicCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MelisPlatformFrameworkSymfonyDemoToolLogicBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MelisPlatformFrameworkSymfonyDemoToolLogicCompiler());
    }
}