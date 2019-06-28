<?php

namespace Grain;

use Grain\Database;
use Grain\Container;
use Grain\EventDispatcher;
use Grain\Router;
use Grain\Template;

abstract class Controller
{
    private $config = array();
    private $container;
    private $eventDispatcher;
    private $router;
    private $template;

    /**
     * Sets the application configuration
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
     * Returns the application configuration
     *
     * @return array
     */
    protected function getConfig()
    {
        return $this->config;
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
     * Returns the container
     *
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the event dispatcher
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
     * Returns the event dispatcher
     *
     * @return EventDispatcher
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Sets the router
     *
     * @param Router $router
     *
     * @return Controller
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Reutnrs the router
     *
     * @return EventDispatcher
     */
    protected function getRouter()
    {
        return $this->router;
    }

    /**
     * Sets the template
     *
     * @param Template $template
     *
     * @return Controller
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Returns the template engine
     *
     * @return Template
     */
    protected function getTemplate()
    {
        return $this->template;
    }

    /**
     * Returns the configuration of a single MySQL database by name
     *
     * @param string $databaseName
     *
     * @return \PDO|null
     */
    protected function getDb($databaseName)
    {
        if (
            !isset($this->config["mysql"])
            || !isset($this->config["mysql"][$databaseName])
        ) {
            return null;
        }

        $config = $this->config["mysql"][$databaseName];

        $database = new Database($config);
        $connection = $database->connect();

        return $connection;
    }

    /**
     * Renders the given template
     *
     * @param string $template The template filename
     * @param array $data The variables to fill the template with
     *
     * @return void
     */
    protected function render($template, $data)
    {
        // get the caller path to reach the Views directory of the project
        $callerFile = \debug_backtrace()[0]["file"];
        $callerPath = \substr($callerFile, 0, \strrpos($callerFile, "/"));

        echo $this->template->render("$callerPath/../Views/$template", $data);
    }

    /**
     * Redirects to the given url
     *
     * @param string $url
     */
    protected function redirect($url)
    {
        \header("Location: $url");
        exit();
    }

    /**
     * Returns the url of a route path by its name
     *
     * @param string $routeName
     * @param array $parameters
     *
     * @return string
     */
    public function generateUrl($routeName, $parameters = array())
    {
        return $this->router->generateUrl($routeName, $parameters);
    }
}
