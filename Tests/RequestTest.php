<?php

namespace Grain\Tests;

use Grain\Request;

class RequestTests extends \PHPUnit_Framework_TestCase
{
    public function testGetParametersNoParameters()
    {
        $request = new Request();
        $parameters = $request->getParameters("/", array(
            "controller" => "MyProject:MyController",
            "parameters" => array(),
            "parameterPositions" => array()
        ), "");
        
        $this->assertEquals(array(), $parameters);
    }

    public function testGetParametersParameterOneAndOnly()
    {
        $request = new Request();
        $parameters = $request->getParameters("/1", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ), "");
        
        $this->assertEquals(array("id" => 1), $parameters);
    }
    
    public function testGetParametersParameterBeginning()
    {
        $request = new Request();
        $parameters = $request->getParameters("/1/edit", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ), "");
        
        $this->assertEquals(array("id" => 1), $parameters);
    }
    
    public function testGetParametersParameterMiddle()
    {
        $request = new Request();
        $parameters = $request->getParameters("/user/1/edit", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(2)
        ), "");
        
        $this->assertEquals(array("id" => 1), $parameters);
    }
    
    public function testGetParametersParameterEnd()
    {
        $request = new Request();
        $parameters = $request->getParameters("/user/edit/1", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(3)
        ), "");
        
        $this->assertEquals(array("id" => 1), $parameters);
    }
}
