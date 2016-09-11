<?php

namespace Grain\Tests;

use Grain\Router;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDbConfigurationDoesNotExist()
    {
        $this->markTestIncomplete();
    }

    public function testGetDbConfigurationExists()
    {
        $this->markTestIncomplete();
    }

    public function testGenerateUrlForNonExistingRoute()
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

    public function testGenerateUrlForExistingRoute()
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
}
