<?php

namespace Grain;

use Grain\Router;
use Grain\Container;
use Grain\EventDispatcher;

class Core
{
    private $config = array();
    private $router;
    private $container;
    private $eventDispatcher;

    /**
     * Used to pass the application parametets to the controllers and instanciate required classes.
     *
     * Core is being instanciated from the front controller.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->router = new Router();
        $this->initializeContainer(array())
            ->initializeEventDispatcher(array());
    }

    /**
     * Handles the requests and retuns a response.
     *
     * It first matches the request to a route and then executes the matched controller.
     * If there is no matched route, it returns a canned response with 404 status.
     *
     * @param string $requestPath
     * @param string $method
     * @param string $contentType
     *
     * @return string
     */
    public function handle($requestPath, $method, $contentType)
    {
        // find the route that matches the request
        $matchedRoute = $this->router->matcher($requestPath, $method);

        $this->eventDispatcher->dispatch("core.post_request");

        if (!$matchedRoute) {
            \header("HTTP/1.0 404 Not Found");
            $response = "Route not found.";

            $this->eventDispatcher->dispatch("core.pre_response");

            return $response;
        }

        // get the request parameters
        $request = new Request();
        $parameters = $request->getParameters($requestPath, $matchedRoute, $contentType);

        // prepare and instanciate the route's controller class
        $controller = $matchedRoute["controller"];
        $controllerArray = \explode(":", $controller);

        $projectName = $controllerArray[0];
        $controllerName = $controllerArray[1];
        $actionName = $controllerArray[2];

        $className = "$projectName\\Controller\\{$controllerName}Controller";
        $controllerMethod = "{$actionName}Action";

        $controllerClass = new $className();

        // add the global configuration to the controller and execute
        $controllerClass->setConfig($this->config);
        $controllerClass->setContainer($this->container)
            ->setEventDispatcher($this->eventDispatcher)
            ->setRouter($this->router);
        $response = $controllerClass->$controllerMethod($parameters);

        // handle array controller responses as JSON responses
        if (\gettype($response) === "array") {
            \header("Content-Type: application/json");
            $response = \json_encode($response);
        }

        $this->eventDispatcher->dispatch("core.pre_response");

        return $response;
    }

    /**
     * Maps the route paths to controllers.
     *
     * It is used by the front controller to register routes and controllers.
     *
     * @param string $routePath
     * @param string $method
     * @param string $controller
     * @param string $routeName
     *
     * @return Core
     */
    public function map($routePath, $method, $controller, $routeName)
    {
        $this->router->addRoute(array(
            "path" => $routePath,
            "method" => $method,
            "controller" => $controller,
            "routeName" => $routeName
        ));

        return $this;
    }

    /**
     * Initializes the container and sets the service definitions
     *
     * @param array $serviceDefinitions
     *
     * @return Core
     */
    public function initializeContainer($serviceDefinitions)
    {
        $this->container = new Container();
        $this->container->loadDefinitions($serviceDefinitions);

        return $this;
    }

    /**
     * Initializes the event dispatcher and sets the service definitions
     *
     * @param array $serviceDefinitions
     *
     * @return Core
     */
    public function initializeEventDispatcher($serviceDefinitions)
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->loadDefinitions($serviceDefinitions);

        return $this;
    }
}
