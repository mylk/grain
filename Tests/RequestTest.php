<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Request;

class RequestTests extends TestCase
{
    public function testGetParametersNoParameters(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/", array(
            "controller" => "MyProject:MyController",
            "parameters" => array(),
            "parameterPositions" => array()
        ), "text/plain");

        $this->assertEquals(array(), $parameters);
    }

    public function testGetParametersParameterOneAndOnly(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/1", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ), "text/plain");

        $this->assertEquals(array("id" => 1), $parameters);
    }

    public function testGetParametersParameterBeginning(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/1/edit", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ), "text/plain");

        $this->assertEquals(array("id" => 1), $parameters);
    }

    public function testGetParametersParameterMiddle(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/user/1/edit", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(2)
        ), "text/plain");

        $this->assertEquals(array("id" => 1), $parameters);
    }

    public function testGetParametersParameterEnd(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/user/edit/1", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(3)
        ), "text/plain");

        $this->assertEquals(array("id" => 1), $parameters);
    }

    public function testGetParametersParameterData(): void
    {
        $request = new Request();

        $parameters = $request->getParameters("/user/?parameter=1", array(
            "controller" => "MyProject:MyController",
            "parameters" => array(),
            "parameterPositions" => array()
        ), "text/plain");

        $this->assertEquals(array("parameter" => "1"), $parameters);
    }

    public function testGetParametersPostData(): void
    {
        $request = $this
            ->getMockBuilder(Request::class)
            ->setMethods(array("getRawData"))
            ->getMock();
        $request->method("getRawData")
            ->willReturn("parameter=1");

        $parameters = $request->getParameters("/user", array(
            "controller" => "MyProject:MyController",
            "parameters" => array(),
            "parameterPositions" => array()
        ), "text/plain");

        $this->assertEquals(array("parameter" => "1"), $parameters);
    }

    public function testGetParametersPostJsonData(): void
    {
        $request = $this
            ->getMockBuilder(Request::class)
            ->setMethods(array("getRawData"))
            ->getMock();
        $request->method("getRawData")
            ->willReturn("{\"parameter\":\"1\"}");

        $parameters = $request->getParameters("/user", array(
            "controller" => "MyProject:MyController",
            "parameters" => array(),
            "parameterPositions" => array()
        ), "application/json");

        $this->assertEquals(array("parameter" => "1"), $parameters);
    }
}
