<?php

namespace Grain\Tests;

use Grain\Core;
use Grain\Router;

require __DIR__ . "/Mocks/Controller/MockStringController.php";
require __DIR__ . "/Mocks/Controller/MockArrayController.php";

class CoreTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $testConfig = array();
        $core = new Core($testConfig);

        $core->map("/", "GET", "MyProject:User:edit", "testRoute");

        $router = $this->readAttribute($core, "router");
        $route = $this->readAttribute($router, "routes")[0];

        $this->assertEquals("/", $route["path"]);
        $this->assertEquals("GET", $route["method"]);
        $this->assertEquals("MyProject:User:edit", $route["controller"]);
        $this->assertEquals("testRoute", $route["routeName"]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandleRequestForNonExistentRoute()
    {
        $testConfig = array();
        $core = new Core($testConfig);

        $response = $core->handle("/", "GET", "text/plain");

        $this->assertEquals("Route not found.", $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandleRequestForExistingRouteReturningString()
    {
        $router = new Router();
        $routerReflection = new \ReflectionClass($router);
        $routesProperty = $routerReflection->getProperty("routes");
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($router, array(
            array(
                "path" => "/",
                "method" => "GET",
                "controllerClassName" => "Grain\Tests\MockStringController",
                "controllerActionName" => "indexAction",
                "parameters" => array(),
                "parameterPositions" => array(),
                "routeName" => "testRoute"
            )
        ));

        $testConfig = array();
        $core = new Core($testConfig);
        $coreReflection = new \ReflectionClass($core);
        $routerProperty = $coreReflection->getProperty("router");
        $routerProperty->setAccessible(true);
        $routerProperty->setValue($core, $router);

        $response = $core->handle("/", "GET", "text/plain");

        $this->assertEquals("string", $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandleRequestForExistingRouteReturningArray()
    {
      $router = new Router();
        $routerReflection = new \ReflectionClass($router);
        $routesProperty = $routerReflection->getProperty("routes");
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($router, array(
            array(
                "path" => "/",
                "method" => "GET",
                "controllerClassName" => "Grain\Tests\MockArrayController",
                "controllerActionName" => "indexAction",
                "parameters" => array(),
                "parameterPositions" => array(),
                "routeName" => "testRoute"
            )
        ));

        $testConfig = array();
        $core = new Core($testConfig);
        $coreReflection = new \ReflectionClass($core);
        $routerProperty = $coreReflection->getProperty("router");
        $routerProperty->setAccessible(true);
        $routerProperty->setValue($core, $router);

        $response = $core->handle("/", "GET", "text/plain");

        $this->assertEquals(array("value1" => 1, "value2" => 2), \json_decode($response, true));
    }
}
