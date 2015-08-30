<?php

namespace Grain;

use Grain\Database;

class Controller
{
    private $config;
    
    /**
     * Stores the database configurations in a private variable.
     * 
     * @param array $config
     * 
     * @return \Grain\Controller
     */
    public function setConfig($config)
    {
        $this->config = $config;
        
        return $this;
    }

    /**
     * Returns the database configurations.
     * 
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Returns the configuration of a single MySQL database by name.
     * 
     * @param string $databaseName
     * 
     * @return \PDO
     */
    public function getDb($databaseName)
    {
        $config = $this->config["mysql"][$databaseName];
        
        $database = new Database($config);
        $connection = $database->connect();
        
        return $connection;
    }
    
    /**
     * Renders the given template.
     * 
     * @param string $template The template filename
     * @param array $parameters The variables to fill the template with
     * 
     * @return void
     */
    public function render($template, $parameters)
    {
        \extract($parameters);
        
        // get the caller path to reach the Views directory of the project
        $callerFile = \debug_backtrace()[0]["file"];
        $callerPath = \substr($callerFile, 0, \strrpos($callerFile, "/"));
        
        require "$callerPath/../Views/$template";
    }
}
