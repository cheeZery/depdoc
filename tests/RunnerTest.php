<?php

namespace DepDocTest;

use DepDoc\Application;
use DepDoc\Runner;
use PHPUnit\Framework\TestCase;

class RunnerTest extends TestCase
{
    public function testRunOutputsHelp()
    {
        $runner = new Runner();

        ob_start();
        $exitCode = $runner->run(['-h']);

        $output = ob_get_contents();
        ob_end_clean();

        $this->assertContains('depdoc [command] [options]', $output);
        $this->assertEquals(0, $exitCode);
    }

    public function testRunDefaultsToValidate()
    {
        $application = $this->prophesize(Application::class);
        $application
            ->validateAction()
            ->willReturn(true);

        $runner = new Runner();
        $runner->setApplication($application->reveal());

        $exitCode = $runner->run();
        $this->assertEquals(0, $exitCode);
    }

    public function testRunUseProvidesArgument()
    {
        $application = $this->prophesize(Application::class);
        $application
            ->validateAction()
            ->willReturn(true);
        $application
            ->updateAction()
            ->willReturn(true);

        $runner = new Runner();
        $runner->setApplication($application->reveal());

        $exitCodeValidate = $runner->run(['validate']);
        $exitCodeUpdate = $runner->run(['update']);

        $this->assertEquals(0, $exitCodeValidate);
        $this->assertEquals(0, $exitCodeUpdate);
    }

    public function testRunFailsOnInvalidCommand()
    {
        $runner = new Runner();
        ob_start();
        $exitCode = $runner->run(['bad']);
        ob_end_clean();

        $this->assertEquals(1, $exitCode);
    }

    public function testRunFailsIfCommandFails()
    {
        $application = $this->prophesize(Application::class);
        $application
            ->validateAction()
            ->willReturn(false);
        $application
            ->updateAction()
            ->willReturn(false);

        $runner = new Runner();
        $runner->setApplication($application->reveal());

        $exitCodeValidate = $runner->run(['validate']);
        $exitCodeUpdate = $runner->run(['update']);

        $this->assertEquals(1, $exitCodeValidate);
        $this->assertEquals(1, $exitCodeUpdate);
    }
}
