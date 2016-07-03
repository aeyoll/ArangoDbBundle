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
    const PREFIX = 'aeyoll_arango_db';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->setDatabaseConfiguration($container, $config);
        $this->setDatabaseOptions($container, $config);

        $this->addClassesToCompile(array(
            'Aeyoll\Bundle\ArangoDbBundle\Database\Manager'
        ));
    }

    /**
     * Define the database configuration
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function setDatabaseConfiguration(ContainerBuilder $container, array $config)
    {
        $container->setParameter(self::PREFIX . '.default_connection', $config['default_connection']);

        foreach ($config['connections'] as $name => $connectionAttributes) {
            foreach ($connectionAttributes as $connectionAttributeName => $connectionAttribute) {
                $key = $this->getConnectionParameterKey($name, $connectionAttributeName);
                $container->setParameter($key, $connectionAttribute);
            }
        }
    }

    /**
     * Set the database options
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function setDatabaseOptions(ContainerBuilder $container, array $config)
    {
        foreach ($config['options'] as $optionName => $option) {
            $container->setParameter(self::PREFIX . '.options.' .$optionName, $option);
        }
    }

    /**
     * Get the key of a connection parameter
     *
     * @param  string $connectionName
     * @param  string $connectionAttributeName
     *
     * @return string
     */
    private function getConnectionParameterKey($connectionName, $connectionAttributeName)
    {
        return self::PREFIX . '.connection.' . $connectionName . '.' . $connectionAttributeName;
    }
}
