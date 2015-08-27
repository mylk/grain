<?php

namespace Grain;

class Request
{
    /**
     * Extracts the parameter values from the request path.
     * 
     * @param string $requestPath
     * @param array $matchedRoute
     * @return array $request
     */
    public function getParameters($requestPath, $matchedRoute)
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
}