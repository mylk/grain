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

        require __DIR__ . "/Views/$template";
    }
}
