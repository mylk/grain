<?php

namespace Grain;

class EventDispatcher
{
    private $definitions = array();

    /**
     * Returns the defined services
     *
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * Loads the service definitions file.
     *
     * @param array|null $definitions
     * @return boolean|void
     * @throws \Exception
     */
    public function loadDefinitions($definitions)
    {
        if (\is_array($definitions) && empty($definitions)) {
            return false;
        } elseif (!$definitions) {
            throw new \Exception("The format of the services definition file is invalid.", 10);
        }

        // get only event listener definitions
        foreach ($definitions as $definition) {
            $this->groupByEvent($definition);
        }
    }

    /**
     * Dispatches an event.
     *
     * Executes event listeners subscribed to given event
     * in the service definitions file.
     *
     * @param string $eventName
     *
     * @return boolean
     */
    public function dispatch($eventName)
    {
        $results = array();

        if (!isset($this->definitions[$eventName])) {
            return false;
        }

        $listenerMethod = $this->getListenerMethodName($eventName);

        $listeners = $this->definitions[$eventName];

        foreach ($listeners as $listener) {
            if (\class_exists($listener["class"]) && \method_exists($listener["class"], $listenerMethod)) {
                $listener = new $listener["class"]();
                $listener->$listenerMethod();
            } else {
                $results[] = false;
            }
        }

        // if any listener class or any listener method did not exist, return false
        if (\in_array(false, $results)) {
            return false;
        }

        return true;
    }

    /**
     * Groups event listeners by subscribed event.
     *
     * @param array $definition
     *
     * @return void
     */
    private function groupByEvent($definition)
    {
        if (!isset($definition["events"])) {
            return;
        }

        foreach ($definition["events"] as $event) {
            if (!isset($this->definitions[$event])) {
                $this->definitions[$event] = array();
            }

            $this->definitions[$event][] = $definition;
        }
    }

    /**
     * Gets the method name to be called on event listeners
     * according to the given event name.
     *
     * @param string $eventNameFull
     *
     * @return string
     */
    private function getListenerMethodName($eventNameFull)
    {
        // strip the event name, usually prefixed with a "class name"
        // like "core", for internal framework events
        $eventNameFullParsed = \explode(".", $eventNameFull);
        $eventName = \end($eventNameFullParsed);

        $eventNameSpaced = \str_replace("_", " ", $eventName);

        $methodName = "on" . \str_replace(" ", "", \ucwords($eventNameSpaced));

        return $methodName;
    }
}
