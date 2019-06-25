<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Router;

class ControllerTest extends TestCase
{
    public function testGetDbConfigurationDoesNotExist(): void
    {
        $this->markTestIncomplete();
    }

    public function testGetDbConfigurationExists(): void
    {
        $this->markTestIncomplete();
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
}
