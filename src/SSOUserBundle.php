<?php

namespace FTVEN\Education\SSOUserBundle;

use FTVEN\Education\SSOUserBundle\DependencyInjection\Compiler\ConnectorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SSOUserBundle
 *
 * @package FTVEN\Education\SSOUserBundle
 */
class SSOUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConnectorCompilerPass());
    }
}