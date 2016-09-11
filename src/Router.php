<?php

namespace Grain;

class Router
{
    private $routes = array();

    /**
     * Adds a new route
     *
     * Also, searches a route path for parameter placeholders
     * and adds them to the route's description.
     *
     * @param array $route
     *
     * @return Router
     */
    public function addRoute($route)
    {
        $parametersPosition = array();

        $matches = array();
        \preg_match_all("/\{(.*?)\}/", $route["path"], $matches);

        $parameters = array();
        if (\count($matches) > 0) {
            $parametersPosition = $this->getPathParameterPositions($route["path"]);
            $parameters = $matches[1];
        }

        // prepare route's controller class and method
        $controller = $route["controller"];
        $controllerArray = \explode(":", $controller);
        $projectName = $controllerArray[0];
        $controllerName = $controllerArray[1];
        $actionName = $controllerArray[2];
        $route["controllerClassName"] = "$projectName\\Controller\\{$controllerName}Controller";
        $route["controllerActionName"] = "{$actionName}Action";

        if (\gettype($route["methods"]) === "string") {
            $route["methods"] = array($route["methods"]);
        }

        $route["parameters"] = $parameters;
        $route["parameterPositions"] = $parametersPosition;

        $this->routes[] = $route;

        return $this;
    }

    /**
     * Checks if a request url matches to any routes and returns
     * the matched route along with its description
     *
     * @param string $requestPath
     * @param string $method
     *
     * @return array | null
     */
    public function matcher($requestPath, $method)
    {
        $requestPathParsed = \parse_url($requestPath);
        $requestPath = isset($requestPathParsed["path"]) ? $requestPathParsed["path"] : $requestPath;

        foreach ($this->routes as $route) {
            // replace the parameter positions of the request url with regex
            $requestPathRegex = $this->buildRequestPathRegex($requestPath, $route);
            
            if (
                \preg_match($requestPathRegex, $route["path"])
                && \in_array($method, $route["methods"])
            ) {
                return $route;
            }
        }
        
        return null;
    }

    /**
     * Finds the positions of parameters in a route path.
     *
     * @param type $routePath
     *
     * @return array
     */
    public function getPathParameterPositions($routePath)
    {
        $pathElements = \explode("/", $routePath);

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

    /**
     * Returns the url of a route path by its name
     *
     * @param string $routeName
     * @param array $parameters
     *
     * @return string $url
     */
    public function generateUrl($routeName, $parameters = array())
    {
        $route = \array_filter($this->routes, function ($element) use ($routeName) {
            return $routeName === $element["routeName"];
        });

        $route = \array_values($route);
        $routePath = $route ? $route[0]["path"] : null;
        if (!$routePath) {
            return null;
        }

        foreach ($parameters as $key => $value) {
            $routePath = \str_replace("{{$key}}", $value, $routePath);
        }

        return $routePath;
    }

    /**
     * Converts the request url to a regular expression to be used
     * to find a match to a route's path.
     *
     * @param string $requestPath
     * @param array $routeOptions
     *
     * @return string
     */
    private function buildRequestPathRegex($requestPath, $routeOptions)
    {
        $requestPathElements = \explode("/", $requestPath);

        // replace the parts of the request url that contain parameters with a regex
        foreach ($routeOptions["parameterPositions"] as $parameterPosition) {
            $requestPathElements[$parameterPosition] = "\{(.*?)\}";
        }

        $regexPattern = "/^" . \str_replace("/", "\/", \implode("/", $requestPathElements)) . "$/";

        return $regexPattern;
    }
}
