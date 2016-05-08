<?php

namespace Grain\Tests;

use Grain\Controller;

class MockStringController extends Controller
{
    public function indexAction()
    {
        return "string";
    }
}
