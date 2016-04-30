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

    public function testRouterRequestWithoutParameters()
    {
        $matchedRoute = $this->router->matcher("/", "GET");
        
        $this->assertEquals($this->findRouteByPath("/"), $matchedRoute);
    }

    public function testRouterRequestOnlyOneParameter()
    {
        $matchedRoute = $this->router->matcher("/1", "GET");
        
        $this->assertEquals($this->findRouteByPath("/{id}"), $matchedRoute);
    }
    
    public function testRouterRequestBeginningParameter()
    {
        $matchedRoute = $this->router->matcher("/1/edit", "GET");
        
        $this->assertEquals($this->findRouteByPath("/{id}/edit"), $matchedRoute);
    }
    
    public function testRouterRequestMiddleParameter()
    {
        $matchedRoute = $this->router->matcher("/user/1/edit", "GET");
        
        $this->assertEquals($this->findRouteByPath("/user/{id}/edit"), $matchedRoute);
    }
    
    public function testRouterRequestEndParameter()
    {
        $matchedRoute = $this->router->matcher("/user/edit/1", "GET");
        
        $this->assertEquals($this->findRouteByPath("/user/edit/{id}"), $matchedRoute);
    }

    public function testRouterNonExistentRoute()
    {
        $matchedRoute = $this->router->matcher("/a/mess", "GET");
        
        $this->assertEquals(null, $matchedRoute);
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
