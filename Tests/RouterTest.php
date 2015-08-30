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
}