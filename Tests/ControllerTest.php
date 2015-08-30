<?php

namespace Grain\Tests;

use Grain\Controller;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetConfig()
    {
        $testConfiguration = array("test" => "config");
        
        $controller = new Controller();
        
        $controllerInstance = $controller->setConfig($testConfiguration);
        
        $this->assertTrue($controllerInstance instanceOf Controller);
        $this->assertEquals($testConfiguration, $controllerInstance->getConfig());
    }
}