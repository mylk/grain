<?php

namespace Grain\Tests;

use Grain\Container;

// require the mocks for testing the container
require __DIR__ . "/Mocks/Container/MockService.php";
require __DIR__ . "/Mocks/Container/MockServiceWithDependency.php";
require __DIR__ . "/Mocks/Container/MockDependency.php";

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Exception
     */
    public function testLoadDefinitionsInvalidDefinitions()
    {
        $container = new Container();

        // this is what is normally done in the front controllers
        $definitions = \json_decode(null, true);
        $container->loadDefinitions($definitions);
    }

    public function testLoadDefinitionsEmptyDefinitions()
    {
        $container = new Container();

        // this is what is normally done in the front controllers
        $definitions = \json_decode("{}", true);
        $result = $container->loadDefinitions($definitions);

        $this->assertFalse($result);
    }

    public function testLoadDefinitionsMissingPublic()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array()
            )
        ));

        $definitions = $this->readAttribute($container, "definitions");

        $this->assertCount(1, $definitions);
    }

    public function testLoadDefinitionsPublicService()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array(),
                "public" => true
            )
        ));

        $definitions = $this->readAttribute($container, "definitions");

        $this->assertCount(1, $definitions);
    }

    public function testLoadDefinitionsNonPublicService()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array(),
                "public" => false
            )
        ));

        $definitions = $this->readAttribute($container, "definitions");

        $this->assertCount(0, $definitions);
    }

    /**
     * @expectedException Exception
     */
    public function testGetWhileNoDefinitionsExist()
    {
        $container = new Container();

        $container->testService;
    }

    /**
     * @expectedException Exception
     */
    public function testGetNotDefinedService()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array(),
                "public" => false
            )
        ));

        $container->TestService;
    }

    public function testGetNotInitializedServiceWithoutDependencies()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "Service" => array(
                "class" => "Grain\Tests\MockService",
                "dependencies" => array(),
                "public" => true
            )
        ));

        $service = $container->Service;
        $this->assertInstanceOf("Grain\Tests\MockService", $service);
    }

    public function testGetNotInitializedServiceWithNotInitializedDependencies()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "Service" => array(
                "class" => "Grain\Tests\MockServiceWithDependency",
                "dependencies" => array("Dependency"),
                "public" => true
            ),
            "Dependency" => array(
                "class" => "Grain\Tests\MockDependency",
                "dependencies" => array(),
                "public" => true
            )
        ));

        $service = $container->Service;
        $this->assertInstanceOf("Grain\Tests\MockServiceWithDependency", $service);
    }

    public function testGetNotInitializedServiceWithInitializedDependencies()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "Service" => array(
                "class" => "Grain\Tests\MockServiceWithDependency",
                "dependencies" => array("Dependency"),
                "public" => true
            ),
            "Dependency" => array(
                "class" => "Grain\Tests\MockDependency",
                "dependencies" => array(),
                "public" => true
            )
        ));

        // initialize the dependency
        $dependency = $container->Dependency;
        $this->assertInstanceOf("Grain\Tests\MockDependency", $dependency);

        $service = $container->Service;
        $this->assertInstanceOf("Grain\Tests\MockServiceWithDependency", $service);

        $dependencyFromService = $this->readAttribute($service, "dependency");
        $this->assertInstanceOf("Grain\Tests\MockDependency", $dependencyFromService);
        $this->assertSame($dependency, $dependencyFromService);
    }

    public function testGetInitializedServiceWithoutDependencies()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "Service" => array(
                "class" => "Grain\Tests\MockService",
                "dependencies" => array(),
                "public" => true
            )
        ));

        $serviceFirst = $container->Service;
        $this->assertInstanceOf("Grain\Tests\MockService", $serviceFirst);

        $serviceSecond = $container->Service;
        $this->assertInstanceOf("Grain\Tests\MockService", $serviceSecond);

        $this->assertSame($serviceFirst, $serviceSecond);
    }

    /**
     * @expectedException Exception
     */
    public function testGetNotInitializedServiceWithNonPublicDependencies()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "Service" => array(
                "class" => "Grain\Tests\MockServiceWithDependency",
                "dependencies" => array("Dependency"),
                "public" => true
            ),
            "Dependency" => array(
                "class" => "Grain\Tests\MockDependency",
                "dependencies" => array(),
                "public" => false
            )
        ));

        $service = $container->Service;
    }

    public function testServiceMissingDependenciesFromDefinitions()
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "Service" => array(
                "class" => "Grain\Tests\MockService",
                "public" => true
            )
        ));

        $serviceFirst = $container->Service;
        $this->assertInstanceOf("Grain\Tests\MockService", $serviceFirst);
    }
}
