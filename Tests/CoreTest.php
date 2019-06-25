<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Core;
use Grain\Router;
use Grain\Container;
use Grain\EventDispatcher;

require __DIR__ . "/Mocks/Controller/MockStringController.php";
require __DIR__ . "/Mocks/Controller/MockArrayController.php";

class CoreTest extends TestCase
{
    public function testGetRouterReturnsRouter(): void
    {
        $testConfig = array();
        $core = new Core($testConfig);

        $this->assertEquals(new Router(), $core->getRouter());
    }

    public function testGetContainerReturnsContainer(): void
    {
        $testConfig = array();
        $core = new Core($testConfig);

        $this->assertEquals(new Container(), $core->getContainer());
    }

    public function testGetEventDispatcherReturnsEventDispatcher(): void
    {
        $testConfig = array();
        $core = new Core($testConfig);

        $this->assertEquals(new EventDispatcher(), $core->getEventDispatcher());
    }

    public function testMap(): void
    {
        $testConfig = array();
        $core = new Core($testConfig);

        $core->map("/", "GET", "MyProject:User:edit", "testRoute");

        $route = $core->getRouter()->getRoutes()[0];

        $this->assertEquals("/", $route["path"]);
        $this->assertEquals(array("GET"), $route["methods"]);
        $this->assertEquals("MyProject:User:edit", $route["controller"]);
        $this->assertEquals("testRoute", $route["routeName"]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandleRequestForNonExistentRoute(): void
    {
        $testConfig = array();
        $core = new Core($testConfig);

        $response = $core->handle("/", "GET", "text/plain");

        $this->assertEquals("Route not found.", $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandleRequestForExistingRouteReturningString(): void
    {
        $router = new Router();
        $routerReflection = new \ReflectionClass($router);
        $routesProperty = $routerReflection->getProperty("routes");
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($router, array(
            array(
                "path" => "/",
                "methods" => array("GET"),
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
    public function testHandleRequestForExistingRouteReturningArray(): void
    {
        $router = new Router();
        $routerReflection = new \ReflectionClass($router);
        $routesProperty = $routerReflection->getProperty("routes");
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($router, array(
            array(
                "path" => "/",
                "methods" => array("GET"),
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

    /**
     * @runInSeparateProcess
     */
    public function testHandleRequestForExistentRouteWithMultipleMethodsMatchingFirst(): void
    {
        $router = new Router();
        $routerReflection = new \ReflectionClass($router);
        $routesProperty = $routerReflection->getProperty("routes");
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($router, array(
            array(
                "path" => "/",
                "methods" => array("GET", "POST"),
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
    public function testHandleRequestForExistentRouteWithMultipleMethodsMatchingSecond(): void
    {
        $router = new Router();
        $routerReflection = new \ReflectionClass($router);
        $routesProperty = $routerReflection->getProperty("routes");
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($router, array(
            array(
                "path" => "/",
                "methods" => array("GET", "POST"),
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

        $response = $core->handle("/", "POST", "text/plain");

        $this->assertEquals("string", $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandleRequestForNonExistentRouteWithMultipleMethods(): void
    {
        $router = new Router();
        $routerReflection = new \ReflectionClass($router);
        $routesProperty = $routerReflection->getProperty("routes");
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($router, array(
            array(
                "path" => "/",
                "methods" => array("GET", "POST"),
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

        $response = $core->handle("/", "OPTIONS", "text/plain");

        $this->assertEquals("Route not found.", $response);
    }
}
