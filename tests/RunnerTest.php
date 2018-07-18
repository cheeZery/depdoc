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

}
