<?php

namespace FTVEN\Education\SSOUserBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
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
        $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('security.yaml');
        $loader->load('clients.yaml');
        $loader->load('validators.yaml');
        $loader->load('builders.yaml');

        if ($container->getParameter('kernel.environment') === "test") {
            $loader->load('clients_test.yaml');
        }
    }
}