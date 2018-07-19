<?php

namespace DepDocTest;

use DepDoc\Application;
use DepDoc\Runner;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

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

    public function testRunDefaultsToHelp()
    {
        $application = $this->prophesize(Application::class);
        /** @noinspection PhpParamsInspection Argument::any() won't fit into <array> .. */
        $application
            ->validateAction(Argument::any())
            ->shouldNotBeCalled();
        /** @noinspection PhpParamsInspection Argument::any() won't fit into <array> .. */
        $application
            ->updateAction(Argument::any())
            ->shouldNotBeCalled();

        $runner = new Runner();
        $runner->setApplication($application->reveal());

        ob_start();
        $exitCode = $runner->run();

        $output = ob_get_contents();
        ob_end_clean();

        $this->assertContains('depdoc [command] [options]', $output);
        $this->assertEquals(0, $exitCode);
    }

    public function testRunUseProvidesArgument()
    {
        $options = [
            'targetDirectory' => getcwd(),
        ];

        $application = $this->prophesize(Application::class);
        $application
            ->validateAction($options)
            ->shouldBeCalled()
            ->willReturn(true);
        $application
            ->updateAction($options)
            ->shouldBeCalled()
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
        $options = [
            'targetDirectory' => getcwd(),
        ];

        $application = $this->prophesize(Application::class);
        $application
            ->validateAction($options)
            ->shouldBeCalled()
            ->willReturn(false);
        $application
            ->updateAction($options)
            ->shouldBeCalled()
            ->willReturn(false);

        $runner = new Runner();
        $runner->setApplication($application->reveal());

        $exitCodeValidate = $runner->run(['validate']);
        $exitCodeUpdate = $runner->run(['update']);

        $this->assertEquals(1, $exitCodeValidate);
        $this->assertEquals(1, $exitCodeUpdate);
    }
}
