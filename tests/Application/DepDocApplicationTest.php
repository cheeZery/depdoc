<?php

namespace DepDocTest\Application;

use DepDoc\Application\DepDocApplication;
use PHPUnit\Framework\TestCase;

class DepDocApplicationTest extends TestCase
{
    public function testIstShouldHaveExpectedCommands()
    {
        $application = new DepDocApplication();

        $this->assertTrue($application->has('update'));
        $this->assertTrue($application->has('validate'));
    }
}
