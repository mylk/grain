<?php

namespace Grain;

class Container
{
    private $definitions = array();
    private $container;
    
    /**
     * Loads the service definitions file.
     * 
     * @param array $serviceDefinitions
     * @return boolean|void
     * @throws Exception
     */
    public function loadDefinitions($serviceDefinitions)
    {
        if (\is_array($serviceDefinitions) && empty($serviceDefinitions)) {
            return false;
        } elseif (!$serviceDefinitions) {
            throw new \Exception("The format of the services definition file is invalid.", 10);
        }

        // get only public service definitions
        foreach ($serviceDefinitions as $definitionName => $definition) {
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
     * @throws Exception
     */
    public function __get($name)
    {
        if (!isset($this->definitions[$name])) {
            throw new \Exception("Service \"$name\" does not exist in services definition.", 20);
        }

        // requested service exists in services definition file
        $className = $this->definitions[$name]["class"];
        $dependencies = isset($this->definitions[$name]["dependencies"]) ? $this->definitions[$name]["dependencies"] : array();

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
    }
}
