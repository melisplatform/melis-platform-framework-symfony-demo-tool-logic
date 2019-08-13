<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic;

use MelisCodeExampleSymfony\TestBundle\DependencyInjection\Compiler\TestCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MelisPlatformFrameworkSymfonyDemoToolLogicBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TestCompiler());
    }
}