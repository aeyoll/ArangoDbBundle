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
            ->children()
                ->scalarNode('default_connection')->defaultNull()->end()
                ->arrayNode('connection')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('database')->end()
                            ->scalarNode('endpoint')->end()
                            ->scalarNode('auth_user')->end()
                            ->scalarNode('auth_passwd')->end()
                            ->scalarNode('auth_type')->defaultValue('Basic')->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('options')
                    ->prototype('array')
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
