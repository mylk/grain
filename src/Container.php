<?php

namespace Grain;

class Container
{
    private $definitions;
    private $container;
    
    /**
     * Loads the services definition file.
     * 
     * @param string $servicesDefinition
     * @throws Exception
     */
    public function loadDefinitions($servicesDefinition)
    {        
        if (!$servicesDefinition) {
            throw new \Exception("The format of the services definition file is invalid.", 10);
        }

        // get only public service definitions
        foreach ($servicesDefinition as $definitionName => $definition) {
            if (
                !isset($definition["public"])
                || (isset($definition["public"]) && $definition["public"])
            ) {
                $this->definitions[$definitionName] = $definition;
            }
        }
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