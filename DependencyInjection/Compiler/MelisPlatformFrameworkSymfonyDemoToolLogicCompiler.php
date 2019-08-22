<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MelisPlatformFrameworkSymfonyDemoToolLogicCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder)
    {
        if($containerBuilder->has('logic_tool.service')){
            $containerBuilder->getDefinition('logic_tool.service')->setPublic(true);
        }
    }
}