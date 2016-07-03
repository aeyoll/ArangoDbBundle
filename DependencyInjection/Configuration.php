<?php

namespace Aeyoll\Bundle\ArangoDbBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use triagens\ArangoDb\UpdatePolicy;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('aeyoll_arango_db');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('default_connection')->defaultValue('default')->end()
                ->arrayNode('connections')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('database')->defaultValue('_system')->end()
                            ->scalarNode('endpoint')->defaultValue('tcp://127.0.0.1:8529')->end()
                            ->scalarNode('auth_user')->end()
                            ->scalarNode('auth_password')->end()
                            ->scalarNode('auth_type')->defaultValue('Basic')->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('options')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('connection')->defaultValue('Keep-Alive')->end()
                            ->integerNode('timeout')->defaultValue(30)->end()
                            ->booleanNode('reconnect')->defaultTrue()->end()
                            ->booleanNode('create')->defaultTrue()->end()
                            ->scalarNode('update_policy')->defaultValue(UpdatePolicy::LAST)->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
