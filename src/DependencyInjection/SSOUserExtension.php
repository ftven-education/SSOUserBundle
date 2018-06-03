<?php

namespace FTVEN\Education\SSOUserBundle\DependencyInjection;

use FTVEN\Education\SSOUserBundle\Service\Connector;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class SSOUserExtension
 *
 * @package FTVEN\Education\SSOUserBundle
 */
class SSOUserExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('security.xml');
        $loader->load('clients.xml');
        $loader->load('validators.xml');
        $loader->load('builders.xml');

        $factory = $container->getDefinition('sso_user.connector.pool');
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