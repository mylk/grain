<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Container;
use Grain\Router;
use Grain\Template;
use Grain\EventDispatcher;

class ControllerTest extends TestCase
{
    public function testGetConfigReturnsEmptyArrayWhenConfigurationNotSet(): void
    {
        $controller = new MockStringController();

        $result = $this->invokePrivateMethod($controller, "getConfig", array());

        $this->assertEmpty($result);
    }

    public function testGetConfigReturnsConfigurationWhenSet(): void
    {
        $config = array("foo" => "bar");

        $controller = new MockStringController();
        $controller->setConfig($config);

        $result = $this->invokePrivateMethod($controller, "getConfig", array());

        $this->assertEquals($config, $result);
    }

    public function testGetRouterReturnsNullWhenNotSet(): void
    {
        $controller = new MockStringController();

        $result = $this->invokePrivateMethod($controller, "getRouter", array());

        $this->assertNull($result);
    }

    public function testGetRouterReturnsRouterWhenSet(): void
    {
        $controller = new MockStringController();
        $controller->setRouter(new Router());

        $result = $this->invokePrivateMethod($controller, "getRouter", array());

        $this->assertEquals(new Router(), $result);
    }

    public function testGetContainerReturnsNullWhenNotSet(): void
    {
        $controller = new MockStringController();

        $result = $this->invokePrivateMethod($controller, "getContainer", array());

        $this->assertNull($result);
    }

    public function testGetContainerReturnsContainerWhenSet(): void
    {
        $controller = new MockStringController();
        $controller->setContainer(new Container());

        $result = $this->invokePrivateMethod($controller, "getContainer", array());

        $this->assertEquals(new Container(), $result);
    }

    public function testGetEventDispatcherReturnsNullWhenNotSet(): void
    {
        $controller = new MockStringController();

        $result = $this->invokePrivateMethod($controller, "getEventDispatcher", array());

        $this->assertNull($result);
    }

    public function testGetEventDispatcherReturnsEventDispatcherWhenSet(): void
    {
        $controller = new MockStringController();
        $controller->setEventDispatcher(new EventDispatcher());

        $result = $this->invokePrivateMethod($controller, "getEventDispatcher", array());

        $this->assertEquals(new EventDispatcher(), $result);
    }

    public function testGetTemplateReturnsNullWhenNotSet(): void
    {
        $controller = new MockStringController();

        $result = $this->invokePrivateMethod($controller, "getTemplate", array());

        $this->assertNull($result);
    }

    public function testGetTemplateReturnsTemplateWhenSet(): void
    {
        $controller = new MockStringController();
        $controller->setTemplate(new Template());

        $result = $this->invokePrivateMethod($controller, "getTemplate", array());

        $this->assertEquals(new Template(), $result);
    }

    public function testGetDbReturnsNullWhenAnyConfigurationNotSet(): void
    {
        $controller = new MockStringController();

        $result = $this->invokePrivateMethod($controller, "getDb", array("foo"));

        $this->assertNull($result);
    }

    public function testGetDbReturnsNullWhenSpecificDatabaseConfigurationNotSet(): void
    {
        $controller = new MockStringController();
        $controller->setConfig(array("mysql" => array()));

        $result = $this->invokePrivateMethod($controller, "getDb", array("foo"));

        $this->assertNull($result);
    }

    public function testGetDbReturnsDatabaseConnectionWhenConfigurationExists(): void
    {
        $this->markTestIncomplete();
    }

    public function testRenderCallsRenderOfTemplateService(): void
    {
        $this->markTestIncomplete();
    }

    public function testRedirectSetsLocationHeader(): void
    {
        $this->markTestIncomplete();
    }

    public function testGenerateUrlReturnsNullWhenRouteDoesNotExist(): void
    {
        $router = new Router();
        $controller = new MockStringController();

        $router->addRoute(array(
            "path" => "/",
            "methods" => array("GET"),
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $controller->setRouter($router);
        $url = $controller->generateUrl("anotherTestRoute");

        $this->assertNull($url);
    }

    public function testGenerateUrlReturnsUrlWhenRouteExists(): void
    {
        $router = new Router();
        $controller = new MockStringController();

        $router->addRoute(array(
            "path" => "/",
            "methods" => array("GET"),
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $controller->setRouter($router);
        $url = $controller->generateUrl("testRoute");

        $this->assertEquals("/", $url);
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
