<?php

namespace Grain;

use Grain\Database;
use Grain\Container;
use Grain\EventDispatcher;

abstract class Controller
{
    private $config;
    private $container;

    /**
     * Stores the database configurations in a private variable.
     *
     * @param array $config
     *
     * @return \Grain\Controller
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Returns the database configurations.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns the configuration of a single MySQL database by name.
     *
     * @param string $databaseName
     *
     * @return \PDO
     */
    protected function getDb($databaseName)
    {
        $config = $this->config["mysql"][$databaseName];

        $database = new Database($config);
        $connection = $database->connect();

        return $connection;
    }

    /**
     * Renders the given template.
     *
     * @param string $template The template filename
     * @param array $parameters The variables to fill the template with
     *
     * @return void
     */
    protected function render($template, $parameters)
    {
        \extract($parameters);

        // get the caller path to reach the Views directory of the project
        $callerFile = \debug_backtrace()[0]["file"];
        $callerPath = \substr($callerFile, 0, \strrpos($callerFile, "/"));

        require "$callerPath/../Views/$template";
    }

    /**
     * Sets the container
     *
     * @param Container $container
     *
     * @return Controller
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Gets the container
     *
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Gets the event dispatcher
     *
     * @param EventDispatcher $eventDispatcher
     *
     * @return Controller
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Gets the event dispatcher
     *
     * @return EventDispatcher
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }
}
