<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Router;

class RouterTest extends TestCase
{
    private $router;
    private $routes = array(
        array(
            "path" => "/",
            "methods" => array("GET"),
            "controller" => "MyProject:MyController:index",
            "parameters" => array(),
            "parameterPositions" => array(),
            "controllerClassName" => "MyProject\Controller\MyControllerController",
            "controllerActionName" => "indexAction"
        ),
        array(
            "path" => "/{id}",
            "methods" => array("GET"),
            "controller" => "MyProject:MyController:index",
            "parameters" => array("id"),
            "parameterPositions" => array(1),
            "controllerClassName" => "MyProject\Controller\MyControllerController",
            "controllerActionName" => "indexAction"
        ),
        array(
            "path" => "/{id}/edit",
            "methods" => array("GET"),
            "controller" => "MyProject:MyController:index",
            "parameters" => array("id"),
            "parameterPositions" => array(1),
            "controllerClassName" => "MyProject\Controller\MyControllerController",
            "controllerActionName" => "indexAction"
        ),
        array(
            "path" => "/user/{id}/edit",
            "methods" => array("GET"),
            "controller" => "MyProject:MyController:index",
            "parameters" => array("id"),
            "parameterPositions" => array(2),
            "controllerClassName" => "MyProject\Controller\MyControllerController",
            "controllerActionName" => "indexAction"
        ),
        array(
            "path" => "/user/edit/{id}",
            "methods" => array("GET"),
            "controller" => "MyProject:MyController:index",
            "parameters" => array("id"),
            "parameterPositions" => array(3),
            "controllerClassName" => "MyProject\Controller\MyControllerController",
            "controllerActionName" => "indexAction"
        ),
        array(
            "path" => "/multiple/methods",
            "methods" => array("GET", "POST"),
            "controller" => "MyProject:MyController:index",
            "parameters" => array(),
            "parameterPositions" => array(),
            "controllerClassName" => "MyProject\Controller\MyControllerController",
            "controllerActionName" => "indexAction"
        ),
    );

    public function setUp(): void
    {
        parent::setUp();

        $this->router = new Router();

        foreach ($this->routes as $route) {
            $this->router->addRoute($route);
        }
    }

    public function testGetRoutesReturnsEmptyArrayWhenNotSet(): void
    {
        $router = new Router();

        $this->assertEmpty($router->getRoutes());
    }

    public function testGetRoutesReturnsRoutesWhenSet(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/user/edit/{id}/{version}",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/user/edit/{id}/{version}",
                    "methods" => array("GET"),
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id", "version"),
                    "parameterPositions" => array(3, 4),
                    "routeName" => "testRoute",
                    "controllerClassName" => "MyProject\Controller\UserController",
                    "controllerActionName" => "editAction"
                )
            ),
            $router->getRoutes()
        );
    }

    public function testMatcherUrlWithoutParameters(): void
    {
        $matchedRoute = $this->router->matcher("/", "GET");

        $this->assertNotNull($matchedRoute);
        $this->assertEquals($this->findRouteByPath("/"), $matchedRoute);
    }

    public function testMatcherUrlOnlyOneParameter(): void
    {
        $matchedRoute = $this->router->matcher("/1", "GET");

        $this->assertNotNull($matchedRoute);
        $this->assertEquals($this->findRouteByPath("/{id}"), $matchedRoute);
    }

    public function testMatcherUrlBeginningParameter(): void
    {
        $matchedRoute = $this->router->matcher("/1/edit", "GET");

        $this->assertNotNull($matchedRoute);
        $this->assertEquals($this->findRouteByPath("/{id}/edit"), $matchedRoute);
    }

    public function testMatcherUrlMiddleParameter(): void
    {
        $matchedRoute = $this->router->matcher("/user/1/edit", "GET");

        $this->assertNotNull($matchedRoute);
        $this->assertEquals($this->findRouteByPath("/user/{id}/edit"), $matchedRoute);
    }

    public function testMatcherUrlEndParameter(): void
    {
        $matchedRoute = $this->router->matcher("/user/edit/1", "GET");

        $this->assertNotNull($matchedRoute);
        $this->assertEquals($this->findRouteByPath("/user/edit/{id}"), $matchedRoute);
    }

    public function testMatcherNonExistingRoute(): void
    {
        $matchedRoute = $this->router->matcher("/a/mess", "GET");

        $this->assertEquals(null, $matchedRoute);
    }

    public function testMatcherRouteWithMultipleParametersMatchFirst(): void
    {
        $matchedRoute = $this->router->matcher("/multiple/methods", "GET");

        $this->assertNotNull($matchedRoute);
        $this->assertEquals($this->findRouteByPath("/multiple/methods"), $matchedRoute);
    }

    public function testMatcherRouteWithMultipleParametersMatchSecond(): void
    {
        $matchedRoute = $this->router->matcher("/multiple/methods", "POST");

        $this->assertNotNull($matchedRoute);
        $this->assertEquals($this->findRouteByPath("/multiple/methods"), $matchedRoute);
    }

    public function testMatcherRouteWithMultipleParametersNoMatch(): void
    {
        $matchedRoute = $this->router->matcher("/multiple/methods", "OPTIONS");

        $this->assertEquals(null, $matchedRoute);
    }

    public function testGenerateUrlNoRoutesExist(): void
    {
        $router = new Router();

        $url = $router->generateUrl("testRoute");

        $this->assertNull($url);
    }

    public function testGenerateUrlNonExistingRoute(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $url = $router->generateUrl("anotherTestRoute");

        $this->assertNull($url);
    }

    public function testGenerateUrlExistingRouteWithoutParameters(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $url = $router->generateUrl("testRoute");

        $this->assertEquals("/", $url);
    }

    public function testGenerateUrlExistingRouteWithSingleParameter(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/{id}",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $url = $router->generateUrl("testRoute", array("id" => 1));

        $this->assertEquals("/1", $url);
    }

    public function testGenerateUrlExistingRouteWithMultipleParameters(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/user/edit/{id}/{version}",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $url = $router->generateUrl("testRoute", array("id" => 1, "version" => 2));

        $this->assertEquals("/user/edit/1/2", $url);
    }

    public function testMapRouteWithOutParameters(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/",
                    "methods" => array("GET"),
                    "controller" => "MyProject:User:edit",
                    "parameters" => array(),
                    "parameterPositions" => array(),
                    "routeName" => "testRoute",
                    "controllerClassName" => "MyProject\Controller\UserController",
                    "controllerActionName" => "editAction"
                )
            ),
            $router->getRoutes()
        );
    }

    public function testMapRouteWithParameterOneAndOnly(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/{id}",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/{id}",
                    "methods" => array("GET"),
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" => array(1),
                    "routeName" => "testRoute",
                    "controllerClassName" => "MyProject\Controller\UserController",
                    "controllerActionName" => "editAction"
                )
            ),
            $router->getRoutes()
        );
    }

    public function testMapRouteWithParameterBeginning(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/{id}/edit",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/{id}/edit",
                    "methods" => array("GET"),
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" => array(1),
                    "routeName" => "testRoute",
                    "controllerClassName" => "MyProject\Controller\UserController",
                    "controllerActionName" => "editAction"
                )
            ),
            $router->getRoutes()
        );
    }

    public function testMapRouteWithParameterMiddle(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/user/{id}/edit",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/user/{id}/edit",
                    "methods" => array("GET"),
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" => array(2),
                    "routeName" => "testRoute",
                    "controllerClassName" => "MyProject\Controller\UserController",
                    "controllerActionName" => "editAction"
                )
            ),
            $router->getRoutes()
        );
    }

    public function testMapRouteWithParameterEnd(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/user/edit/{id}",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/user/edit/{id}",
                    "methods" => array("GET"),
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" => array(3),
                    "routeName" => "testRoute",
                    "controllerClassName" => "MyProject\Controller\UserController",
                    "controllerActionName" => "editAction"
                )
            ),
            $router->getRoutes()
        );
    }

    public function testMapRouteWithTwoParameters(): void
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/user/edit/{id}/{version}",
            "methods" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/user/edit/{id}/{version}",
                    "methods" => array("GET"),
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id", "version"),
                    "parameterPositions" => array(3, 4),
                    "routeName" => "testRoute",
                    "controllerClassName" => "MyProject\Controller\UserController",
                    "controllerActionName" => "editAction"
                )
            ),
            $router->getRoutes()
        );
    }

    /**
     * @dataProvider getRoutePaths
     */
    public function testGetPathParameterPositions($routePath, $parameterPositions): void
    {
        $this->assertEquals($parameterPositions, $this->router->getPathParameterPositions($routePath));
    }

    public function getRoutePaths()
    {
        return array(
            array("/{id}", array(1)),
            array("/{id}/edit", array(1)),
            array("/user/{id}/edit", array(2)),
            array("/user/edit/{id}", array(3)),
            array("/{id}/{version}", array(1, 2)),
            array("/{id}/edit/{version}", array(1, 3)),
            array("/user/{id}/edit/{version}", array(2, 4))
        );
    }

    private function findRouteByPath($path)
    {
        $route = \array_filter($this->routes, function ($element) use ($path) {
            return $path === $element["path"];
        });

        $route = \array_values($route);

        return ($route) ? $route[0] : null;
    }
}
