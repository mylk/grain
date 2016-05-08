<?php

namespace Grain\Tests;

use Grain\Controller;

class MockArrayController extends Controller
{
    public function indexAction()
    {
        return array("value1" => 1, "value2" => 2);
    }
}
