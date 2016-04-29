<?php

namespace Grain;

class EventDispatcher
{
    private $definitions;

    /**
     * Loads the services definition file.
     *
     * @param type $definitions
     * @return boolean|void
     * @throws Exception
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
            if (isset($definition["events"])) {
                $this->groupByEvent($definition);
            }
        }
    }

    public function dispatch($eventName)
    {
        $listeners = $this->definitions[$eventName];
        if (!$listeners) {
            return false;
        }

        $listenerMethod = $this->getListenerMethodName($eventName);

        foreach ($listeners as $listener) {
            if (\class_exists($listener["class"]) && \method_exists($listener["class"], $listenerMethod)){
                $listener = new $listener["class"]();
                $listener->$listenerMethod();
            }
        }
    }

    private function groupByEvent($definition)
    {
        foreach ($definition["events"] as $event) {
            if (!isset($this->definitions[$event])) {
                $this->definitions[$event] = array();
            }

            $this->definitions[$event][] = $definition;
        }
    }

    private function getListenerMethodName($eventNameFull)
    {
        // strip the event name, usually prefixed with a "class name"
        // like "core", for internal framework events
        $eventName = \end(\explode(".", $eventNameFull));

        $eventNameSpaced = \str_replace("_", " ", $eventName);

        $methodName = "on" . \str_replace(" ", "", (\ucwords($eventNameSpaced)));

        return $methodName;
    }
}
