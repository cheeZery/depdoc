<?php

namespace DepDocTest\Application;

use DepDoc\Application\DepDocApplication;
use PHPUnit\Framework\TestCase;

class DepDocApplicationTest extends TestCase
{
    public function testItShouldHaveExpectedCommands()
    {
        $application = new DepDocApplication();

        $this->assertEquals('DepDoc', $application->getName());
        $this->assertTrue($application->has('update'));
        $this->assertTrue($application->has('validate'));
    }
}
