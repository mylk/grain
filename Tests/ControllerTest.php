<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Router;
use Grain\Template;

class ControllerTest extends TestCase
{
    public function testGetDbConfigurationDoesNotExist(): void
    {
        $this->markTestIncomplete();
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

    public function testGenerateUrlForNonExistingRoute(): void
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

    public function testGenerateUrlForExistingRoute(): void
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
