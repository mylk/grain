<?php

namespace Grain;

class Router
{
    /**
     * Checks if a request url matches to any routes and returns
     * the matched route along with its description
     * 
     * @param array $routes
     * @param string $requestPath
     * 
     * @return array | null
     */
    public function matcher($routes, $requestPath)
    {
        foreach ($routes as $routePath => $settings) {
            // replace the parameter positions of the request url with regex
            $requestPathRegex = $this->buildRequestPathRegex($requestPath, $settings);
            
            if (\preg_match($requestPathRegex, $routePath)) {
                return $routes[$routePath];
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
