<?php

namespace Grain\Tests;

use Grain\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    private $router;
    private $routes = array(
        array(
            "path" => "/",
            "method" => "GET",
            "controller" => "MyProject:MyController",
            "parameters" => array(),
            "parameterPositions" => array()
        ),
        array(
            "path" => "/{id}",
            "method" => "GET",
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ),
        array(
            "path" => "/{id}/edit",
            "method" => "GET",
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ),
        array(
            "path" => "/user/{id}/edit",
            "method" => "GET",
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(2)
        ),
        array(
            "path" => "/user/edit/{id}",
            "method" => "GET",
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(3)
        )
    );

    public function setUp()
    {
        parent::setUp();

        $this->router = new Router();

        foreach ($this->routes as $route) {
            $this->router->addRoute($route);
        }
    }

    public function testMatcherUrlWithoutParameters()
    {
        $matchedRoute = $this->router->matcher("/", "GET");

        $this->assertEquals($this->findRouteByPath("/"), $matchedRoute);
    }

    public function testMatcherUrlOnlyOneParameter()
    {
        $matchedRoute = $this->router->matcher("/1", "GET");

        $this->assertEquals($this->findRouteByPath("/{id}"), $matchedRoute);
    }

    public function testMatcherUrlBeginningParameter()
    {
        $matchedRoute = $this->router->matcher("/1/edit", "GET");

        $this->assertEquals($this->findRouteByPath("/{id}/edit"), $matchedRoute);
    }

    public function testMatcherUrlMiddleParameter()
    {
        $matchedRoute = $this->router->matcher("/user/1/edit", "GET");

        $this->assertEquals($this->findRouteByPath("/user/{id}/edit"), $matchedRoute);
    }

    public function testMatcherUrlEndParameter()
    {
        $matchedRoute = $this->router->matcher("/user/edit/1", "GET");

        $this->assertEquals($this->findRouteByPath("/user/edit/{id}"), $matchedRoute);
    }

    public function testMatcherNonExistingRoute()
    {
        $matchedRoute = $this->router->matcher("/a/mess", "GET");

        $this->assertEquals(null, $matchedRoute);
    }

    public function testGenerateUrlNoRoutesExist()
    {
        $router = new Router();

        $url = $router->generateUrl("testRoute");

        $this->assertNull($url);
    }

    public function testGenerateUrlNonExistingRoute()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $url = $router->generateUrl("anotherTestRoute");

        $this->assertNull($url);
    }

    public function testGenerateUrlExistingRouteWithoutParameters()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $url = $router->generateUrl("testRoute");

        $this->assertEquals("/", $url);
    }

    public function testGenerateUrlExistingRouteWithSingleParameter()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/{id}",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $url = $router->generateUrl("testRoute", array("id" => 1));

        $this->assertEquals("/1", $url);
    }

    public function testGenerateUrlExistingRouteWithMultipleParameters()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/user/edit/{id}/{version}",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $url = $router->generateUrl("testRoute", array("id" => 1, "version" => 2));

        $this->assertEquals("/user/edit/1/2", $url);
    }

    public function testMapRouteWithOutParameters()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array(),
                    "parameterPositions" => array(),
                    "routeName" => "testRoute"
                )
            ),
            $this->readAttribute($router, "routes")
        );
    }

    public function testMapRouteWithParameterOneAndOnly()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/{id}",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/{id}",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" => array(1),
                    "routeName" => "testRoute"
                )
            ),
            $this->readAttribute($router, "routes")
        );
    }

    public function testMapRouteWithParameterBeginning()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/{id}/edit",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/{id}/edit",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" => array(1),
                    "routeName" => "testRoute"
                )
            ),
            $this->readAttribute($router, "routes")
        );
    }

    public function testMapRouteWithParameterMiddle()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/user/{id}/edit",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/user/{id}/edit",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" => array(2),
                    "routeName" => "testRoute"
                )
            ),
            $this->readAttribute($router, "routes")
        );
    }

    public function testMapRouteWithParameterEnd()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/user/edit/{id}",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/user/edit/{id}",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" => array(3),
                    "routeName" => "testRoute"
                )
            ),
            $this->readAttribute($router, "routes")
        );
    }

    public function testMapRouteWithTwoParameters()
    {
        $router = new Router();

        $router->addRoute(array(
            "path" => "/user/edit/{id}/{version}",
            "method" => "GET",
            "controller" => "MyProject:User:edit",
            "routeName" => "testRoute"
        ));

        $this->assertEquals(
            array(
                array(
                    "path" => "/user/edit/{id}/{version}",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id", "version"),
                    "parameterPositions" => array(3, 4),
                    "routeName" => "testRoute"
                )
            ),
            $this->readAttribute($router, "routes")
        );
    }

    /**
     * @dataProvider getRoutePaths
     */
    public function testGetPathParameterPositions($routePath, $parameterPositions)
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
