<?php

namespace DepDocTest\Command;

use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommandTest extends TestCase
{
    /** @var PHPProphet */
    protected $prophet;

    protected function setUp()
    {
        $this->prophet = new PHPProphet();
    }

    public function testItConfiguresDirectoryOption()
    {
        $command = new BaseCommandTestDouble('test');
        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('directory'));

        $option = $definition->getOption('directory');
        $this->assertEquals('directory', $option->getName());
        $this->assertEquals('d', $option->getShortcut());
        $this->assertTrue($option->isValueRequired());
        $this->assertEquals(getcwd(), $option->getDefault());
    }

    public function testItValidatesInputDirectoryCorrectly()
    {
        $prophecy = $this->prophet->prophesize('DepDoc\\Command');

        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $input->getOption('directory')->willReturn('/test/dir')->shouldBeCalled();

        $prophecy->realpath('/test/dir')->willReturn(true)->shouldBeCalled();

        $output->getVerbosity()->willReturn(OutputInterface::VERBOSITY_NORMAL)->shouldBeCalled();
        $output->isVerbose()->willReturn(false)->shouldBeCalled();
        $output
            ->getFormatter()
            ->willReturn($this->prophesize(OutputFormatterInterface::class)->reveal())
            ->shouldBeCalled();

        $prophecy->reveal();

        $command = new BaseCommandTestDouble('test');
        $result = $command->runExecute($input->reveal(), $output->reveal());

        $this->assertEquals(0, $result);

        $this->prophet->checkPredictions();
    }

    public function testItVerboseOutputsTargetDirectory()
    {
        $prophecy = $this->prophet->prophesize('DepDoc\\Command');

        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $input->getOption('directory')->willReturn('/test/dir')->shouldBeCalled();

        $prophecy->realpath('/test/dir')->willReturn(true)->shouldBeCalled();

        $output->getVerbosity()->willReturn(OutputInterface::VERBOSITY_VERBOSE)->shouldBeCalled();
        $output->isVerbose()->willReturn(true)->shouldBeCalled();
        $output
            ->getFormatter()
            ->willReturn($this->prophesize(OutputFormatterInterface::class)->reveal())
            ->shouldBeCalled();
        $output->writeln('<info>Target directory:</info> /test/dir', 1)->shouldBeCalled();

        $prophecy->reveal();

        $command = new BaseCommandTestDouble('test');
        $result = $command->runExecute($input->reveal(), $output->reveal());

        $this->assertEquals(0, $result);

        $this->prophet->checkPredictions();
    }

    public function testItStopsOnEmptyDirectoryOption()
    {
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $input->getOption('directory')->willReturn('')->shouldBeCalled();

        $output->getVerbosity()->willReturn(OutputInterface::VERBOSITY_NORMAL)->shouldBeCalled();
        $output->isDecorated()->willReturn(false)->shouldBeCalled();
        $output
            ->getFormatter()
            ->willReturn($this->prophesize(OutputFormatterInterface::class)->reveal())
            ->shouldBeCalled();
        $output->write("\n")->shouldBeCalled();
        $output->writeln(Argument::containingString('<fg=white;bg=red> [ERROR] Invalid target directory given: '), 1)->shouldBeCalled();

        $command = new BaseCommandTestDouble('test');
        $result = $command->runExecute($input->reveal(), $output->reveal());

        $this->assertEquals(-1, $result);
    }

    public function testItStopsOnInvalidDirectoryOption()
    {
        $prophecy = $this->prophet->prophesize('DepDoc\\Command');

        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $input->getOption('directory')->willReturn('/test/dir')->shouldBeCalled();

        $prophecy->realpath('/test/dir')->willReturn(false)->shouldBeCalled();

        $output->getVerbosity()->willReturn(OutputInterface::VERBOSITY_NORMAL)->shouldBeCalled();
        $output->isDecorated()->willReturn(false)->shouldBeCalled();
        $output
            ->getFormatter()
            ->willReturn($this->prophesize(OutputFormatterInterface::class)->reveal())
            ->shouldBeCalled();
        $output->write("\n")->shouldBeCalled();
        $output->writeln(Argument::containingString('<fg=white;bg=red> [ERROR] Invalid target directory given: '), 1)->shouldBeCalled();

        $prophecy->reveal();

        $command = new BaseCommandTestDouble('test');
        $result = $command->runExecute($input->reveal(), $output->reveal());

        $this->assertEquals(-1, $result);
    }
}
