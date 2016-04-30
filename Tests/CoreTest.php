<?php

namespace Grain\Tests;

use Grain\Core;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    public function testMapRouteWithOutParameters()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/", "GET", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                array(
                    "path" => "/",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array(),
                    "parameterPositions" =>  array()
                )
            ),
            $this->readAttribute($core, "routes")
        );
    }
    
    public function testMapRouteWithParameterOneAndOnly()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/{id}", "GET", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                array(
                    "path" => "/{id}",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" =>  array(1)
                )
            ),
            $this->readAttribute($core, "routes")
        );
    }
    
    public function testMapRouteWithParameterBeginning()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/{id}/edit", "GET", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                array(
                    "path" => "/{id}/edit",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" =>  array(1)
                )
            ),
            $this->readAttribute($core, "routes")
        );
    }
    
    public function testMapRouteWithParameterMiddle()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/user/{id}/edit", "GET", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                array(
                    "path" => "/user/{id}/edit",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" =>  array(2)
                )
            ),
            $this->readAttribute($core, "routes")
        );
    }
    
    public function testMapRouteWithParameterEnd()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/user/edit/{id}", "GET", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                array(
                    "path" => "/user/edit/{id}",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id"),
                    "parameterPositions" =>  array(3)
                )
            ),
            $this->readAttribute($core, "routes")
        );
    }
    
    public function testMapRouteWithTwoParameters()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/user/edit/{id}/{version}", "GET", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                array(
                    "path" => "/user/edit/{id}/{version}",
                    "method" => "GET",
                    "controller" => "MyProject:User:edit",
                    "parameters" => array("id", "version"),
                    "parameterPositions" =>  array(3, 4)
                )
            ),
            $this->readAttribute($core, "routes")
        );
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testHandleRequestNonExistentRoute()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $response = $core->handle("/", "GET");
        
        $this->assertEquals("Route not found.", $response);
    }
}
