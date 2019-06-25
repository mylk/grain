<?php

namespace Grain\Tests;

use Grain\Tests\MockDependency;

class MockServiceWithDependency
{
    private $dependency;

    public function __construct(MockDependency $dependency)
    {
        $this->dependency = $dependency;
    }

    public function getDependency()
    {
        return $this->dependency;
    }
}
