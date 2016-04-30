<?php

namespace Grain;

class Request
{
    /**
     * Extracts the parameter values from the request path.
     * 
     * Parameter positions in the requested URLs are based
     * on the pattern of the defined routes.
     * 
     * @param string $requestPath
     * @param array $matchedRoute
     * 
     * @return array $request
     */
    public function getParameters($requestPath, $matchedRoute)
    {
        $requestPathElements = \explode("/", $requestPath);
        
        $requestParameters = array();
        foreach ($matchedRoute["parameterPositions"] as $parameterPosition) {
            $requestParameters[] = $requestPathElements[$parameterPosition];
        }

        // create an array with the route parameter name as a key
        // and the request value as a value
        $request = \array_combine($matchedRoute["parameters"], $requestParameters);

        // get any data sent with the request
        $requestData = \file_get_contents("php://input");
        if ($requestData) {
            \parse_str($requestData, $requestDataArray);
            $request = \array_merge($request, $requestDataArray);
        }

        return $request;
    }
}
