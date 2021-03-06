<?php

namespace DepDocTest\Application;

use DepDoc\Application\DepDocApplication;
use DepDoc\Command\UpdateCommand;
use DepDoc\Command\ValidateCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;

class DepDocApplicationTest extends TestCase
{
    use ProphecyTrait;

    public function testItShouldHaveExpectedCommands()
    {
        $validateCommand = $this->prophesize(ValidateCommand::class);
        $validateCommand->setApplication(Argument::type(Application::class))->willReturn(null);
        $validateCommand->isEnabled()->willReturn(true);
        $validateCommand->getDefinition()->willReturn($this->prophesize(InputDefinition::class)->reveal());
        $validateCommand->getName()->willReturn('validate');
        $validateCommand->getAliases()->willReturn([]);

        $updateCommand = $this->prophesize(UpdateCommand::class);
        $updateCommand->setApplication(Argument::type(Application::class))->willReturn(null);
        $updateCommand->isEnabled()->willReturn(true);
        $updateCommand->getDefinition()->willReturn($this->prophesize(InputDefinition::class)->reveal());
        $updateCommand->getName()->willReturn('update');
        $updateCommand->getAliases()->willReturn([]);

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('ValidateCommand')->willReturn($validateCommand->reveal());
        $container->get('UpdateCommand')->willReturn($updateCommand->reveal());

        $application = new DepDocApplication($container->reveal());

        self::assertEquals('DepDoc', $application->getName());
        self::assertTrue($application->has('update'));
        self::assertTrue($application->has('validate'));
    }
}
