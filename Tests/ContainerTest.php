<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Container;

// require the mocks for testing the container
require __DIR__ . "/Mocks/Container/MockService.php";
require __DIR__ . "/Mocks/Container/MockServiceWithDependency.php";
require __DIR__ . "/Mocks/Container/MockDependency.php";

class ContainerTest extends TestCase
{
    public function testGetDefinitionsReturnsEmptyDefinitionsWhenNotSet(): void
    {
        $container = new Container();
        $this->assertEmpty($container->getDefinitions());
    }

    public function testGetDefinitionsReturnsDefinitionsWhenSet(): void
    {
        $container = new Container();

        $container->loadDefinitions(array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array()
            )
        ));

        $definitions = $container->getDefinitions();
        $this->assertNotEmpty($definitions);
    }

    public function testLoadDefinitionsInvalidDefinitions(): void
    {
        $this->expectException(\Exception::class);

        $container = new Container();

        // this is what is normally done in the front controllers
        $definitions = \json_decode(null, true);
        $container->loadDefinitions($definitions);
    }

    public function testLoadDefinitionsEmptyDefinitions(): void
    {
        $container = new Container();

        // this is what is normally done in the front controllers
        $definitions = \json_decode("{}", true);
        $result = $container->loadDefinitions($definitions);

        $this->assertFalse($result);
    }

    public function testLoadDefinitionsMissingPublic(): void
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array()
            )
        ));

        $this->assertCount(1, $container->getDefinitions());
    }

    public function testLoadDefinitionsPublicService(): void
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array(),
                "public" => true
            )
        ));

        $this->assertCount(1, $container->getDefinitions());
    }

    public function testLoadDefinitionsNonPublicService(): void
    {
        $container = new Container();
        $container->loadDefinitions(array(
            "TestService" => array(
                "class" => "TestApp\Service\TestService",
                "dependencies" => array(),
                "public" => false
            )
        ));

        $this->assertCount(0, $container->getDefinitions());
    }

    public function testGetWhileNoDefinitionsExist(): void
    {
        $this->expectException(\Exception::class);

        $container = new Container();

        $container->testService;
    }

    public function testGetNotDefinedService(): void
    {
        $this->expectException(\Exception::class);

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

    public function testGetNotInitializedServiceWithoutDependencies(): void
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

    public function testGetNotInitializedServiceWithNotInitializedDependencies(): void
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

    public function testGetNotInitializedServiceWithInitializedDependencies(): void
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

        $dependencyService = $service->getDependency();
        $this->assertInstanceOf("Grain\Tests\MockDependency", $dependencyService);
        $this->assertSame($dependency, $dependencyService);
    }

    public function testGetInitializedServiceWithoutDependencies(): void
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

    public function testGetNotInitializedServiceWithNonPublicDependencies(): void
    {
        $this->expectException(\Exception::class);

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

        $container->Service;
    }

    public function testServiceMissingDependenciesFromDefinitions(): void
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
