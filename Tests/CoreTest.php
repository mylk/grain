<?php

namespace Grain\Tests;

use Grain\Core;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    public function testMapRouteWithOutParameters()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/", "GET", "MyProject:User:edit", "testRoute");
        
        $router = $this->readAttribute($core, "router");
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
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/{id}", "GET", "MyProject:User:edit", "testRoute");
        
        $router = $this->readAttribute($core, "router");
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
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/{id}/edit", "GET", "MyProject:User:edit", "testRoute");
        
        $router = $this->readAttribute($core, "router");
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
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/user/{id}/edit", "GET", "MyProject:User:edit", "testRoute");
        
        $router = $this->readAttribute($core, "router");
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
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/user/edit/{id}", "GET", "MyProject:User:edit", "testRoute");
        
        $router = $this->readAttribute($core, "router");
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
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/user/edit/{id}/{version}", "GET", "MyProject:User:edit", "testRoute");
        
        $router = $this->readAttribute($core, "router");
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
     * @runInSeparateProcess
     */
    public function testHandleRequestNonExistentRoute()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $response = $core->handle("/", "GET", "testRoute");
        
        $this->assertEquals("Route not found.", $response);
    }
}
