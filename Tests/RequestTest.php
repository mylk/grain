<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Request;

class RequestTest extends TestCase
{
    public function testGetParametersReturnsEmptyArrayWhenNoParametersExist(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/", array(
            "controller" => "MyProject:MyController",
            "parameters" => array(),
            "parameterPositions" => array()
        ), "text/plain");

        $this->assertEquals(array(), $parameters);
    }

    public function testGetParametersReturnsParameterWhenOneExists(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/1", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ), "text/plain");

        $this->assertEquals(array("id" => 1), $parameters);
    }

    public function testGetParametersReturnsParameterWhenExistsInTheBeginningOfUri(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/1/edit", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(1)
        ), "text/plain");

        $this->assertEquals(array("id" => 1), $parameters);
    }

    public function testGetParametersReturnsParameterWhenExistsInTheMiddleOfUri(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/user/1/edit", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(2)
        ), "text/plain");

        $this->assertEquals(array("id" => 1), $parameters);
    }

    public function testGetParametersReturnsParameterWhenExistsInTheEndOfUri(): void
    {
        $request = new Request();
        $parameters = $request->getParameters("/user/edit/1", array(
            "controller" => "MyProject:MyController",
            "parameters" => array("id"),
            "parameterPositions" => array(3)
        ), "text/plain");

        $this->assertEquals(array("id" => 1), $parameters);
    }

    public function testGetParametersReturnsParameterWhenQueryExists(): void
    {
        $request = new Request();

        $parameters = $request->getParameters("/user/?parameter=1", array(
            "controller" => "MyProject:MyController",
            "parameters" => array(),
            "parameterPositions" => array()
        ), "text/plain");

        $this->assertEquals(array("parameter" => "1"), $parameters);
    }

    public function testGetParametersReturnsParameterWhenPostDataExist(): void
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

    public function testGetParametersReturnsParameterWhenJsonPostDataExist(): void
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

    public function testGetRawDataReturnsRawData(): void
    {
        $this->markTestIncomplete();
    }
}
