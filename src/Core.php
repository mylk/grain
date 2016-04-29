<?php

namespace Grain;

use Grain\Router;
use Grain\Container;
use Grain\EventDispatcher;

class Core
{
    private $routes = array();
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
     * 
     * @return string
     */
    public function handle($requestPath)
    {
        // find the route that matches the request
        $matchedRoute = $this->router->matcher($this->routes, $requestPath);

        $this->eventDispatcher->dispatch("core.post_request");

        if ($matchedRoute) {
            // get the request parameters
            $request = new Request();
            $parameters = $request->getParameters($requestPath, $matchedRoute);

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
                ->setEventDispatcher($this->eventDispatcher);
            $response = $controllerClass->$controllerMethod($parameters);

            // handle array controller responses as JSON responses
            if (\gettype($response) === "array") {
                \header("Content-Type: application/json");
                $response = \json_encode($response);
            }
        } else {
            \header("HTTP/1.0 404 Not Found");
            $response = "Route not found.";
        }
        
        $this->eventDispatcher->dispatch("core.pre_response");

        return $response;
    }

    /**
     * Maps the route paths to controllers.
     * 
     * It is used by the front controller to register routes and controllers.
     * Also, it searches a route path for pararmeter placeholder and populates
     * the route's description.
     * 
     * @param string $routePath
     * @param string $controller
     * 
     * @return Core
     */
    public function map($routePath, $controller)
    {
        $parametersPosition = array();
        
        $matches = array();
        \preg_match_all("/\{(.*?)\}/", $routePath, $matches);
        
        $parameters = array();
        if (\count($matches) > 0) {
            $parametersPosition = Router::getPathParameterPositions($routePath);
            $parameters = $matches[1];
        }

        $this->routes[$routePath] = array(
            "controller" => $controller,
            "parameters" => $parameters,
            "parameterPositions" => $parametersPosition
        );

        return $this;
    }
    
    public function initializeContainer($servicesDefinition)
    {        
        $this->container = new Container();
        $this->container->loadDefinitions($servicesDefinition);
        
        return $this;
    }
    
    public function initializeEventDispatcher($servicesDefinition)
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->loadDefinitions($servicesDefinition);
            
        return $this;
    }
}
