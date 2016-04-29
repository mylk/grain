<?php

namespace Grain\Tests;

use Grain\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    private $routes = array(
        "/" => array(
            "controller" => "MyProject:MyController",
            "parameters" => array(),
            "parameterPositions" => array()
        ),
        "/{id}" => array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ),
        "/{id}/edit" => array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ),
        "/user/{id}/edit" => array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(2)
        ),
        "/user/edit/{id}" => array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(3)
        )
    );
    
    public function testRouterRequestWithoutParameters()
    {
        $router = new Router();
        
        $matchedRoute = $router->matcher($this->routes, "/");
        
        $this->assertEquals($this->routes["/"], $matchedRoute);
    }

    public function testRouterRequestOnlyOneParameter()
    {
        $router = new Router();
        
        $matchedRoute = $router->matcher($this->routes, "/1");
        
        $this->assertEquals($this->routes["/{id}"], $matchedRoute);
    }
    
    public function testRouterRequestBeginningParameter()
    {
        $router = new Router();
        
        $matchedRoute = $router->matcher($this->routes, "/1/edit");
        
        $this->assertEquals($this->routes["/{id}/edit"], $matchedRoute);
    }
    
    public function testRouterRequestMiddleParameter()
    {
        $router = new Router();
        
        $matchedRoute = $router->matcher($this->routes, "/user/1/edit");
        
        $this->assertEquals($this->routes["/user/{id}/edit"], $matchedRoute);
    }
    
    public function testRouterRequestEndParameter()
    {
        $router = new Router();
        
        $matchedRoute = $router->matcher($this->routes, "/user/edit/1");
        
        $this->assertEquals($this->routes["/user/edit/{id}"], $matchedRoute);
    }

    public function testRouterNonExistentRoute()
    {
        $router = new Router();
        
        $matchedRoute = $router->matcher($this->routes, "/a/mess");
        
        $this->assertEquals(null, $matchedRoute);
    }

    /**
     * @dataProvider getRoutePaths
     */
    public function testGetPathParameterPositions($routePath, $parameterPositions)
    {
        $this->assertEquals($parameterPositions, Router::getPathParameterPositions($routePath));
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
}
