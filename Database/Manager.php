<?php

namespace Aeyoll\Bundle\ArangoDbBundle\Database;

use Symfony\Component\DependencyInjection\ContainerInterface;
use triagens\ArangoDb\Connection;
use triagens\ArangoDb\GraphHandler;
use triagens\ArangoDb\EdgeHandler;
use triagens\ArangoDb\Graph;
use triagens\ArangoDb\EdgeDefinition;

class Manager
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var triagens\ArangoDb\Connection|null
     */
    protected $connection = null;

    /**
     * @var triagens\ArangoDb\GraphHandler|null
     */
    protected $graphHandler = null;

    /**
     * @var triagens\ArangoDb\EdgeHandler|null
     */
    protected $edgeHandler = null;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get the Arango connection
     *
     * @return triagens\ArangoDb\Connection
     */
    public function getConnection()
    {
        if (is_null($this->connection)) {
            $connectionOptions = $this->getConnectionOptions();
            $connection        = new Connection($connectionOptions);

            $this->connection = $connection;
        }

        return $this->connection;
    }

    /**
     * Get the Arango graph handler
     *
     * @return triagens\ArangoDb\GraphHandler
     */
    public function getGraphHandler()
    {
        if (is_null($this->graphHandler)) {
            $connection   = $this->getConnection();
            $graphHandler = new GraphHandler($connection);

            $this->graphHandler = $graphHandler;
        }

        return $this->graphHandler;
    }

    /**
     * Get the Arango edge handler
     *
     * @return triagens\ArangoDb\EdgeHandler
     */
    public function getEdgeHandler()
    {
        if (is_null($this->edgeHandler)) {
            $connection   = $this->getConnection();
            $edgeHandler = new EdgeHandler($connection);

            $this->edgeHandler = $edgeHandler;
        }

        return $this->edgeHandler;
    }

    /**
     * Creates a new graph
     *
     * @param string $key
     * @param string $from
     * @param string $to
     */
    public function createGraph(string $key, string $from, string $to)
    {
        $graph = new Graph();
        $graph->set('_key', $key);
        $graph->addEdgeDefinition(new EdgeDefinition($key, $from, $to));

        $this->getGraphHandler()->createGraph($graph);
    }
}
