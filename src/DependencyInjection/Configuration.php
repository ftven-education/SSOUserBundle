<?php

namespace FTVEN\Education\SSOUserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package FTVEN\Education\SSOUserBundle
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sso_user');

        $rootNode
            ->children()
                ->arrayNode('connectors')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        //->arrayPrototype()
                            ->children()
                                ->scalarNode('login_url')->isRequired()->end()
                                ->scalarNode('logout_url')->isRequired()->end()
                                ->scalarNode('validate_url')->isRequired()->end()
                                /*->arrayNode('for_environments')
                                    ->scalarPrototype()->end()
                                ->end()*/
                            ->end()
                        //->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}