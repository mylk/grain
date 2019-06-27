<?php

namespace Grain\Tests;

use PHPUnit\Framework\TestCase;
use Grain\Exception\TemplateNotFoundException;
use Grain\Template;

class TemplateTest extends TestCase
{
    public function testRenderThrowsTemplateNotFoundExceptionWhenTemplateDoesNotExist(): void
    {
        $this->expectException(TemplateNotFoundException::class);

        if (\file_exists("/tmp/grain_test_template.php")) {
            \unlink("/tmp/grain_test_template.php");
        }

        $template = new Template();
        $template->render("/tmp/grain_test_template.php", array("foo" => "bar"));
    }

    public function testRenderReturnsRenderedTemplateWhenTemplateIsPhp(): void
    {
        file_put_contents("/tmp/grain_test_template.php", 'Hello <?=$foo;?>');

        $template = new Template();
        $result = $template->render("/tmp/grain_test_template.php", array("foo" => "bar"));
        $this->assertEquals("Hello bar", $result);
    }

    public function testRenderReturnsOutputWhenTplTemplateRequested(): void
    {
        file_put_contents("/tmp/grain_test_template.tpl", 'Hello %foo');

        $template = new Template();
        $result = $template->render("/tmp/grain_test_template.tpl", array("foo" => "bar"));
        $this->assertEquals("Hello bar", $result);
    }

    public function testRenderReturnsTemplateWithTruncatedVariablesWhenTemplateIsTplAndNoVariableValuesGiven(): void
    {
        file_put_contents("/tmp/grain_test_template.tpl", 'Hello %foo');

        $template = new Template();
        $result = $template->render("/tmp/grain_test_template.tpl", array());
        $this->assertEquals("Hello ", $result);
    }
}
