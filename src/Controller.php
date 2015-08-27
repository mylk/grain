<?php

namespace Grain;

use Grain\Database;

class Controller
{
    private $config;
    
    public function setConfig($config)
    {
        $this->config = $config;
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function getDb($databaseName)
    {
        $config = $this->getConfig()["mysql"][$databaseName];
        
        $database = new Database($config);
        $connection = $database->connect();
        
        return $connection;
    }
    
    public function render($template, $parameters)
    {
        \extract($parameters);
        
        // get the caller path to reach the Views directory
        $callerFile = \debug_backtrace()[0]["file"];
        $callerPath = \substr($callerFile, 0, \strrpos($callerFile, "/"));
        
        require "$callerPath/../Views/$template";
    }
}
