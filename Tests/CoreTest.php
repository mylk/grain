<?php

namespace Grain\Tests;

use Grain\Core;

class CoreTest extends \PHPUnit_Framework_TestCase
{
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
