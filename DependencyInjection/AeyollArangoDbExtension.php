<?php

namespace Aeyoll\Bundle\ArangoDbBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AeyollArangoDbExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('aeyoll_arango_db.default_connection', $config['default_connection']);

        foreach ($config['connections'] as $name => $connectionAttributes) {
            foreach ($connectionAttributes as $connectionAttributeName => $connectionAttribute) {
                $container->setParameter('aeyoll_arango_db.connection.' . $name . '.' . $connectionAttributeName, $connectionAttribute);
            }
        }

        foreach ($config['options'] as $optionName => $option) {
            $container->setParameter('aeyoll_arango_db.options.' .$optionName, $option);
        }

        $this->addClassesToCompile(array(
            'Aeyoll\Bundle\ArangoDbBundle\Database\Manager'
        ));
    }
}
