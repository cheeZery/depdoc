<?php

namespace DepDocTest\Command;

use DepDoc\Configuration\ConfigurationService;
use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\NodePackageManager;
use DepDoc\PackageManager\Package\ComposerPackage;
use DepDoc\PackageManager\Package\NodePackage;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\HelperSet;
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
        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble('test');

        $command->setHelperSet($helperSet->reveal());

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

        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble('test');

        $command->setHelperSet($helperSet->reveal());
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

        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble('test');

        $command->setHelperSet($helperSet->reveal());

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
        $output->writeln(Argument::containingString('<fg=white;bg=red> [ERROR] Invalid target directory given: '),
            1)->shouldBeCalled();

        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble('test');

        $command->setHelperSet($helperSet->reveal());

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
        $output->writeln(Argument::containingString('<fg=white;bg=red> [ERROR] Invalid target directory given: '),
            1)->shouldBeCalled();

        $prophecy->reveal();

        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble('test');

        $command->setHelperSet($helperSet->reveal());

        $result = $command->runExecute($input->reveal(), $output->reveal());

        $this->assertEquals(-1, $result);
    }

    public function testItUsesOptionalConstructorParameters()
    {
        $composerManager = $this->prophesize(ComposerPackageManager::class);
        $nodeManager = $this->prophesize(NodePackageManager::class);
        $configurationService = $this->prophesize(ConfigurationService::class);

        $command = new BaseCommandTestDouble(
            null,
            $composerManager->reveal(),
            $nodeManager->reveal(),
            $configurationService->reveal()
        );

        $this->assertEquals($composerManager->reveal(), $command->getComposerManager());
        $this->assertEquals($nodeManager->reveal(), $command->getNodeManager());
        $this->assertEquals($configurationService->reveal(), $command->getConfigurationService());

        $command = new BaseCommandTestDouble();
        $this->assertNull($command->getComposerManager());
        $this->assertInstanceOf(NodePackageManager::class, $command->getNodeManager());
        $this->assertInstanceOf(ConfigurationService::class, $command->getConfigurationService());
    }

    public function testItCombinesAllInstalledPackages()
    {
        $targetDirectory = '/some/dir';

        $composerManager = $this->prophesize(ComposerPackageManager::class);
        $nodeManager = $this->prophesize(NodePackageManager::class);
        $configurationService = $this->prophesize(ConfigurationService::class);
        $composerPackageList = $this->prophesize(PackageManagerPackageList::class);
        $nodePackageList = $this->prophesize(PackageManagerPackageList::class);
        $composerPackage1 = $this->getComposerPackage('t1');
        $composerPackage2 = $this->getComposerPackage('t2');
        $composerPackage3 = $this->getComposerPackage('t3');
        $nodePackage1 = $this->getNodePackage('t1');
        $nodePackage2 = $this->getNodePackage('t2');
        $nodePackage3 = $this->getNodePackage('t3');

        $composerManager->getInstalledPackages($targetDirectory)->willReturn($composerPackageList->reveal());
        $nodeManager->getInstalledPackages($targetDirectory)->willReturn($nodePackageList->reveal());

        $composerPackageList->getAllFlat()->willReturn([
            $composerPackage1->reveal(),
            $composerPackage2->reveal(),
            $composerPackage3->reveal(),
        ]);

        $nodePackageList->getAllFlat()->willReturn([
            $nodePackage1->reveal(),
            $nodePackage2->reveal(),
            $nodePackage3->reveal(),
        ]);

        $command = new BaseCommandTestDouble(
            null,
            $composerManager->reveal(),
            $nodeManager->reveal(),
            $configurationService->reveal()
        );

        $packages = $command->testGetInstalledPackages($targetDirectory);
        $this->assertTrue($packages->has('Composer', 't1'));
        $this->assertTrue($packages->has('Composer', 't2'));
        $this->assertTrue($packages->has('Composer', 't3'));
        $this->assertTrue($packages->has('Node', 't1'));
        $this->assertTrue($packages->has('Node', 't2'));
        $this->assertTrue($packages->has('Node', 't3'));
        $this->assertCount(6, $packages->getAllFlat());
    }

    /**
     * @param string $name
     * @return ComposerPackage|\Prophecy\Prophecy\ObjectProphecy
     */
    protected function getComposerPackage(string $name)
    {
        $package = $this->prophesize(ComposerPackage::class);

        $package->getManagerName()->willReturn('Composer');
        $package->getName()->willReturn($name);

        return $package;
    }

    /**
     * @param string $name
     * @return NodePackage|\Prophecy\Prophecy\ObjectProphecy
     */
    protected function getNodePackage(string $name)
    {
        $package = $this->prophesize(NodePackage::class);

        $package->getManagerName()->willReturn('Node');
        $package->getName()->willReturn($name);

        return $package;
    }
}
