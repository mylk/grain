<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\EventDispatcher;

// require the mocks for testing the event dispatcher
require __DIR__ . "/Mocks/EventDispatcher/MockEventListener.php";

class EventDispatcherTest extends TestCase
{
    public function testGetDefinitionsReturnsEmptyDefinitionsWhenNotSet(): void
    {
        $eventDispatcher = new EventDispatcher();
        $this->assertEmpty($eventDispatcher->getDefinitions());
    }

    public function testGetDefinitionsReturnsDefinitionsWhenSet(): void
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->loadDefinitions(array(
            "TestEventListener" => array(
                "class" => "TestApp\EventListener\TestEventListner",
                "events" => array("core.post_request")
            )
        ));

        $definitions = $eventDispatcher->getDefinitions();
        $this->assertNotEmpty($definitions);
    }

    public function testLoadDefinitionsInvalidDefinitions(): void
    {
        $this->expectException(\Exception::class);

        $container = new EventDispatcher();

        // this is what is normally done in the front controllers
        $definitions = \json_decode(null, true);
        $container->loadDefinitions($definitions);
    }

    public function testLoadDefinitionsEmptyDefinitions(): void
    {
        $container = new EventDispatcher();

        // this is what is normally done in the front controllers
        $definitions = \json_decode("{}", true);
        $result = $container->loadDefinitions($definitions);

        $this->assertFalse($result);
    }

    public function testLoadDefinitionsMissingEvents(): void
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->loadDefinitions(array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array(),
                "public" => true
            )
        ));

        $this->assertEmpty($eventDispatcher->getDefinitions());
    }

    public function testLoadDefinitionsHavingEvents(): void
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->loadDefinitions(array(
            "TestEventListener" => array(
                "class" => "TestApp\EventListener\TestEventListner",
                "events" => array("core.post_request")
            )
        ));

        $definitions = $eventDispatcher->getDefinitions();
        // listener has been grouped by event name
        $this->assertNotEmpty($definitions["core.post_request"]);

        $this->assertEquals(array(
                "class" => "TestApp\EventListener\TestEventListner",
                "events" => array("core.post_request")
            ),
            $definitions["core.post_request"][0]
        );
    }

    public function testGroupByEventMissingEvents(): void
    {
        $eventDispatcher = new EventDispatcher();

        $this->invokePrivateMethod($eventDispatcher, "groupByEvent", array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array(),
                "public" => true
            )
        ));

        $this->assertEmpty($eventDispatcher->getDefinitions());
    }

    public function testGroupByEventHavingEvents(): void
    {
        $eventDispatcher = new EventDispatcher();

        $this->invokePrivateMethod($eventDispatcher, "groupByEvent", array(
            "TestEventListener" => array(
                "class" => "TestApp\EventListener\TestEventListner",
                "events" => array("core.post_request")
            )
        ));

        $definitions = $eventDispatcher->getDefinitions();
        // listener has been grouped by event name
        $this->assertNotEmpty($definitions["core.post_request"]);

        $this->assertEquals(array(
                "class" => "TestApp\EventListener\TestEventListner",
                "events" => array("core.post_request")
            ),
            $definitions["core.post_request"][0]
        );
    }

    public function testgetListenerMethodNameMissingClassName(): void
    {
        $eventDispatcher = new EventDispatcher();

        $methodName = $this->invokePrivateMethod($eventDispatcher, "getListenerMethodName", array("post_request"));

        $this->assertEquals("onPostRequest", $methodName);
    }

    public function testgetListenerMethodNameHavingClassName(): void
    {
        $eventDispatcher = new EventDispatcher();

        $methodName = $this->invokePrivateMethod($eventDispatcher, "getListenerMethodName", array("core.post_request"));

        $this->assertEquals("onPostRequest", $methodName);
    }

    public function testgetListenerMethodNameNotSnakeCased(): void
    {
        $eventDispatcher = new EventDispatcher();

        $methodName = $this->invokePrivateMethod($eventDispatcher, "getListenerMethodName", array("postRequest"));

        $this->assertEquals("onPostRequest", $methodName);
    }

    public function testDispatchNoDefinitions(): void
    {
        $eventDispatcher = new EventDispatcher();

        $result = $eventDispatcher->dispatch("test");

        $this->assertFalse($result);
    }

    public function testDispatchNoListenersForEvent(): void
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->loadDefinitions(array(
            "TestEventListener" => array(
                "class" => "TestApp\EventListener\TestEventListner",
                "events" => array("core.post_request")
            )
        ));

        $result = $eventDispatcher->dispatch("test");

        $this->assertFalse($result);
    }

    public function testDispatchNonExistingClass(): void
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->loadDefinitions(array(
            "MockEventListener" => array(
                "class" => "SomethingThatWillFail",
                "events" => array("core.post_request")
            )
        ));

        $result = $eventDispatcher->dispatch("core.post_request");

        $this->assertFalse($result);
    }

    public function testDispatchExistingClass(): void
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->loadDefinitions(array(
            "MockEventListener" => array(
                "class" => "Grain\Tests\MockEventListener",
                "events" => array("core.post_request")
            )
        ));

        $result = $eventDispatcher->dispatch("core.post_request");

        $this->assertTrue($result);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokePrivateMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(\get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
