<?php

namespace Grain;

class Core
{
    private $routes = array();
    private $config = array();

    /**
     * Used to pass the application parametets to the controllers.
     * 
     * Core is being instanciated from the front controller.
     * 
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Handles the requests and retuns a response.
     * 
     * It first matches the request to a route and then executes the matched controller.
     * If there is no matched route, it returns a canned response with 404 status.
     * 
     * @param string $requestPath
     * @return string
     */
    public function handle($requestPath)
    {
        // find the route tha matches the request
        $matchedRoute = $this->routeMatcher($requestPath);

        if ($matchedRoute) {
            // get the request parameters
            $request = $this->buildRequest($requestPath, $matchedRoute);

            // prepare and instanciate the route's controller class
            $controller = $matchedRoute["controller"];
            $controllerArray = \explode(":", $controller);
            
            $projectName = $controllerArray[0];
            $controllerName = $controllerArray[1];
            $actionName = $controllerArray[2];
            
            $className = "$projectName\\Controller\\$controllerName";
            $controllerMethod = "{$actionName}Action";

            $controllerClass = new $className();

            // add the global configuration to the controller and execute
            $controllerClass->setConfig($this->config);
            $response = $controllerClass->$controllerMethod($request);

            // handle array controller responses as JSON responses
            if (\gettype($response) === "array") {
                \header("Content-Type: application/json");
                $response = \json_encode($response);
            }
        } else {
            \header("HTTP/1.0 404 Not Found");
            $response = "Route not found.";
        }

        return $response;
    }

    /**
     * Maps the route paths to controllers.
     * 
     * It is used by the front controller to register routes and controllers.
     * 
     * @param string $routePath
     * @param string $controller
     * @return Core
     */
    public function map($routePath, $controller)
    {
        $parametersPosition = array();
        
        $regex = "/\{(.*?)\}/";
        $matches = array();
        \preg_match_all($regex, $routePath, $matches);
        
        $parameters = array();
        if (\count($matches) > 0) {
            $parametersPosition = $this->getPathParameterPositions($routePath);
            $parameters = $matches[1];
        }

        $this->routes[$routePath] = array(
            "controller" => $controller,
            "parameters" => $parameters,
            "parameterPositions" => $parametersPosition
        );

        return $this;
    }
    
    /**
     * Checks if a request path matches to a route.
     * 
     * Retuns the matched route.
     * 
     * @param string $requestPath
     * @return array
     */
    private function routeMatcher($requestPath)
    {
        foreach ($this->routes as $routePath => $settings) {
            // replace request path parameters with regex
            $requestPathRegex = $this->buildRequestPathRegex($requestPath, $settings);
            
            if (\preg_match($requestPathRegex, $routePath)) {
                return $this->routes[$routePath];
            }
        }
    }
    
    /**
     * Extracts the parameter values from the request path.
     * 
     * @param string $requestPath
     * @param array $matchedRoute
     * @return array $request
     */
    private function buildRequest($requestPath, $matchedRoute)
    {
        // get the parameters of the request path
        $pathElements = \explode("/", $requestPath);
        $requestParameters = array();
        foreach ($matchedRoute["parameterPositions"] as $parameterPosition) {
            $requestParameters[] = $pathElements[$parameterPosition];
        }

        // create an array with the route parameter name as a key
        // and the request value as a value
        $request = \array_combine($matchedRoute["parameters"], $requestParameters);

        return $request;
    }
    
    /**
     * Converts the request path to a regular expression to be used
     * to find a match to a route path.
     * 
     * @param string $requestPath
     * @param array $routeOptions
     * @return string
     */
    private function buildRequestPathRegex($requestPath, $routeOptions)
    {
        $pathElements = \explode("/", $requestPath);

        foreach ($routeOptions["parameterPositions"] as $parameterPosition) {
            $pathElements[$parameterPosition] = "\{(.*?)\}";
        }

        $regexPattern = "/^" . \str_replace("/", "\/", \implode("/", $pathElements)) . "$/";
            
        return $regexPattern;
    }
    
    /**
     * Finds the positions of parameters in a route path
     * 
     * @param type $requestPath
     * @return array
     */
    private function getPathParameterPositions($requestPath)
    {
        $pathElements = \explode("/", $requestPath);

        $parametersPos = array();
        $elementPos = 0;
        foreach ($pathElements as $element) {
            if (\preg_match("/\{(.*?)\}/", $element)) {
                $parametersPos[] = $elementPos;
            }
            $elementPos++;
        }

        return $parametersPos;
    }
}