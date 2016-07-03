<?php

namespace Aeyoll\Bundle\ArangoDbBundle\Database;

use Symfony\Component\DependencyInjection\ContainerInterface;
use triagens\ArangoDb\Connection;
use triagens\ArangoDb\GraphHandler;
use triagens\ArangoDb\EdgeHandler;
use triagens\ArangoDb\Graph;
use triagens\ArangoDb\EdgeDefinition;
use triagens\ArangoDb\Statement;

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
     * @param  string $key
     * @param  string $from
     * @param  string $to
     *
     * @return triagens\ArangoDb\Graph
     */
    public function createGraph(string $key, string $from, string $to)
    {
        $graph = new Graph();
        $graph->set('_key', $key);
        $graph->addEdgeDefinition(new EdgeDefinition($key, $from, $to));

        $this->getGraphHandler()->createGraph($graph);

        return $graph;
    }

    /**
     * Create a new Vertex
     *
     * @param  string $key
     * @param  array  $attributes
     *
     * @return triagens\ArangoDb\Vertex
     */
    public function createVertex(string $key, array $attributes)
    {
        return Vertex::createFromArray(array_merge(array('_key' => $key), $attributes));
    }

    /**
     * Attach a vertex to a graph
     *
     * @param  mixed    $graph          Graph name as a string or instance of Graph
     * @param  mixed    $document       Vertex to be added, can be passed as a vertex object or an array
     * @param  string   $collection     Collection name to store the vertex
     *
     * @return string                   Created vertex id
     */
    public function attachVertexToGraph($graph, $document, string $collection)
    {
        return $this->getGraphHandler()->saveVertex($graph, $document, $collection);
    }

    /**
     * Connect two documents in a Graph
     *
     * @param  mixed $graph             Graph name as a string or instance of Graph
     * @param  mixed $from              "From" vertex
     * @param  mixed $to                "To" vertex
     * @param  array $edgeAttributes    Extra attributes to be added in the edge
     *
     * @return string                   Created document id
     */
    public function connectVertices($graph, $from, $to, array $edgeAttributes = array())
    {
        $edge = Edge::createFromArray($edgeAttributes);

        return $this
            ->edgeHandler
            ->saveEdge(
                $graph,
                $from->getHandle(),
                $to->getHandle(),
                $edge
            );
    }

    /**
     * Get the cursor of a query
     *
     * @param  string $query
     * @param  array  $vars
     */
    public function getCursor(string $query, array $vars = [])
    {
        $statement = new Statement($this->getConnection(), array(
            'query'    => $query,
            'bindVars' => $vars
        ));

        $cursor = $statement->execute();

        return $cursor;
    }

    public function getResults($cursor)
    {
        return $cursor->getAll();
    }

    public function getStatistics($cursor)
    {
        return $cursor->getExtra();
    }
}
