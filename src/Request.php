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
     * @param string $contentType
     * 
     * @return array $request
     */
    public function getParameters($requestPath, $matchedRoute, $contentType)
    {
        $requestDataArray = array();
        $requestPathElements = \explode("/", $requestPath);
        
        $requestParameters = array();
        foreach ($matchedRoute["parameterPositions"] as $parameterPosition) {
            $requestParameters[] = $requestPathElements[$parameterPosition];
        }

        // create an array with the route parameter name as a key
        // and the request value as a value
        $request = \array_combine($matchedRoute["parameters"], $requestParameters);

        // get any data sent with the request
        $requestData = $this->getRawData();

        // get data set in the url query
        $requestPathParsed = \parse_url($requestPath);
        if (isset($requestPathParsed["query"])) {
            \parse_str($requestPathParsed["query"], $requestDataArray);
            $request = \array_merge($request, $requestDataArray);
        }

        // get data if request is of application/json content type
        if ($requestData) {
            if ("application/json" === $contentType) {
                $requestDataArray = \json_decode($requestData, true);
                $request = \array_merge($request, $requestDataArray);
            } else {
                \parse_str($requestData, $requestDataArray);
                $request = \array_merge($request, $requestDataArray);
            }
        }

        return $request;
    }

    /**
     * Returns the data sent with the request.
     *
     * Isolated for testability reasons.
     *
     * @return string|null
     */
    protected function getRawData()
    {
        return \file_get_contents("php://input");
    }
}
