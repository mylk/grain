<?php

namespace Grain\Tests;

use Grain\Core;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getRoutePaths
     */
    public function testGetPathParameterPositions($routePath, $parameterPositions)
    {
        $core = new Core($dummyConfig = array());
        
        $this->assertEquals($parameterPositions, $this->invokeMethod($core, "getPathParameterPositions", array($routePath)));
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
    
    /**
     * Call a protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(\get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
