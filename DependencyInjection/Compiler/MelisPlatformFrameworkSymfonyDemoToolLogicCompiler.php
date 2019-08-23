<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MelisPlatformFrameworkSymfonyDemoToolLogicCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $containerBuilder
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        /**
         * Make our logic tool services public
         */
        if($containerBuilder->has('logic_tool.service')){
            $containerBuilder->getDefinition('logic_tool.service')->setPublic(true);
        }
    }
}