<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Core;
use Grain\Router;
use Grain\Container;
use Grain\EventDispatcher;
use Grain\Template;

require __DIR__ . "/Mocks/Controller/MockStringController.php";
require __DIR__ . "/Mocks/Controller/MockArrayController.php";

class CoreTest extends TestCase
{
    public function testConstructorPreparesServices()
    {
        $core = $this->getMockBuilder(Core::class)
            ->setMethods(["initializeContainer", "initializeEventDispatcher"])
            ->disableOriginalConstructor()
            ->getMock();
        $core->expects($this->once())
            ->method("initializeContainer")
            ->willReturnSelf();
        $core->expects($this->once())
            ->method("initializeEventDispatcher")
            ->willReturnSelf();

        $core->__construct(array("foo"));

        $this->assertEquals(array("foo"), $core->getConfig());
        $this->assertEquals(new Router(), $core->getRouter());
        $this->assertEquals(new Template(), $core->getTemplate());
    }

    public function testGetConfigReturnsConfigurationGivenDuringInitialization(): void
    {
        $testConfig = array("foo");
        $core = new Core($testConfig);

        $this->assertEquals($testConfig, $core->getConfig());
    }

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

    public function testGetTemplateReturnsTemplate(): void
    {
        $testConfig = array();
        $core = new Core($testConfig);

        $this->assertEquals(new Template(), $core->getTemplate());
    }

    public function testMapAddsRoute(): void
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
    public function testHandleReturnsNotFoundWhenNoRoutes(): void
    {
        $testConfig = array();
        $core = new Core($testConfig);

        $response = $core->handle("/", "GET", "text/plain");

        $this->assertEquals("Route not found.", $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandleReturnsResponseWhenRouteExistsAndReturnsString(): void
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
    public function testHandleReturnsResponseWhenRouteExistsAndReturnsArray(): void
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
    public function testHandleReturnsMatchesGetMethodWhenMultipleMethodsExistForRoute(): void
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
    public function testHandleReturnsMatchesPostMethodWhenMultipleMethodsExistForRoute(): void
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
    public function testHandleReturnsNotFoundWhenRouteDoesNotSupportTheRequestedMethod(): void
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

    public function testHandleDispatchesPostRequestEvent()
    {
        $this->markTestIncomplete();
    }

    public function testHandleDispatchesPreResponseEventAndReturnsNotFoundWhenRouteDoesNotExist()
    {
        $this->markTestIncomplete();
    }

    public function testHandleDispatchesPreResponseEventWhenRouteExists()
    {
        $this->markTestIncomplete();
    }

    public function testHandleDoesNotSetUpControllerWhenRouteDoesNotExist()
    {
        $this->markTestIncomplete();
    }

    public function testHandleSetsUpControllerWhenRouteExists()
    {
        $this->markTestIncomplete();
    }

    public function testHandleReturnsPlainTextResponseWhenControllerResponseIsString()
    {
        $this->markTestIncomplete();
    }

    public function testHandleReturnsJsonEncodedResponseWhenControllerResponseIsArray()
    {
        $this->markTestIncomplete();
    }

    public function testInitializeContainerSetsUpServices(): void
    {
        $this->markTestIncomplete();
    }

    public function testInitializeEventDispatcherSetsUpServices(): void
    {
        $this->markTestIncomplete();
    }
}
