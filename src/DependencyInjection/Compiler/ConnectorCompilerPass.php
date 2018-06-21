<?php

namespace FTVEN\Education\SSOUserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use FTVEN\Education\SSOUserBundle\Service\Connector;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ConnectorCompilerPass
 *
 * @package FTVEN\Education\SSOUserBundle\DependencyInjection\Compiler
 */
class ConnectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig('sso_user');
        $factory = $container->getDefinition('sso_user.connector.pool');
        $config = reset($configs);
        foreach ($config['connectors'] as $name => $environments) {
            if (!$container->hasDefinition(sprintf('sso_user.build.%s', $name))) {
                throw new \Exception(sprintf('You must implement the service id [sso_user.build.%s]', $name));
            }
            $definition = $container
                ->register('sso_user.connector.'.$name, Connector::class)
                ->setFactory([new Reference('sso_user.factory.connector'), 'getService'])
                ->addArgument($environments)
                ->addArgument($container->getDefinition(sprintf('sso_user.build.%s', $name)));

            $factory->addMethodCall('addConnector', [$name, $definition]);
        }
    }
}