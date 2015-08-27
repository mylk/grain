<?php

namespace Grain;

class Router
{
    /**
     * Checks if a request path matches to a route.
     * 
     * Retuns the matched route.
     * 
     * @param array $routes
     * @param string $requestPath
     * @return array
     */
    public function matcher($routes, $requestPath)
    {
        foreach ($routes as $routePath => $settings) {
            // replace request path parameters with regex
            $requestPathRegex = $this->buildRequestPathRegex($requestPath, $settings);
            
            if (\preg_match($requestPathRegex, $routePath)) {
                return $routes[$routePath];
            }
        }
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
}
