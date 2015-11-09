<?php

namespace Grain;

class Container
{
    private $container;
    private $definitions;
    
    /**
     * Loads the services definition file.
     * 
     * @param type $definitionsPath
     * @throws Exception
     */
    public function loadDefinition($definitionsPath)
    {
        $definitions = null;
        if (\file_exists($definitionsPath)) {
            $definitions = \json_decode(\file_get_contents($definitionsPath), true);
        }
        
        if (!$definitions) {
            throw new \Exception("The format of the services definition file is invalid.", 10);
        }
        
        $this->definitions = $definitions;
    }
    
    /**
     * The magic method that gets called when a service is requested
     * 
     * @param string $name The name of the service
     * 
     * @return object
     */
    public function __get($name)
    {
        // requested service exists in services definition file
        if (isset($this->definitions[$name])) {
            $className = $this->definitions[$name]["class"];
            $dependencies = $this->definitions[$name]["dependencies"];

            // if the service has not been initialized yet
            if (!isset($this->container[$className])) {
                // request the service dependencies also
                $dependenciesResolved = array();
                foreach ($dependencies as $dependency) {
                    $dependenciesResolved[] = $this->$dependency;
                }
                
                // initialize the requested service, adding the dependencies
                $reflector = new \ReflectionClass($className);
                $this->container[$className] = $reflector->newInstanceArgs($dependenciesResolved);
            }

            return $this->container[$className];
        } else {
            throw new \Exception("Service \"$name\" does not exist in services definition.", 20);
        }
    }
}