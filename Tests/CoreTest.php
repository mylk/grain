<?php

namespace Grain\Tests;

use Grain\Core;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    public function testMapRouteWithOutParameters()
    {
        $testConfig = array();
        $core = new Core($testConfig);
        
        $core->map("/", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                "/" => array(
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
        
        $core->map("/{id}", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                "/{id}" => array(
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
        
        $core->map("/{id}/edit", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                "/{id}/edit" => array(
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
        
        $core->map("/user/{id}/edit", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                "/user/{id}/edit" => array(
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
        
        $core->map("/user/edit/{id}", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                "/user/edit/{id}" => array(
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
        
        $core->map("/user/edit/{id}/{version}", "MyProject:User:edit");
        
        $this->assertEquals(
            array(
                "/user/edit/{id}/{version}" => array(
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
        
        $response = $core->handle("/");
        
        $this->assertEquals("Route not found.", $response);
    }
}
