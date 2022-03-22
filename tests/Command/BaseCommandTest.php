<?php

namespace DepDocTest\Command;

use DepDoc\Configuration\ApplicationConfiguration;
use DepDoc\Configuration\ConfigurationService;
use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\NodePackageManager;
use DepDoc\PackageManager\Package\ComposerPackage;
use DepDoc\PackageManager\Package\NodePackage;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommandTest extends TestCase
{
    use ProphecyTrait;

    protected PHPProphet $globalProphet;

    protected function setUp(): void
    {
        $this->globalProphet = new PHPProphet();
    }

    public function testItConfiguresDirectoryOption()
    {
        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble(
            'test',
            $this->prophesize(ComposerPackageManager::class)->reveal(),
            $this->prophesize(NodePackageManager::class)->reveal(),
            $this->prophesize(ConfigurationService::class)->reveal()
        );

        $command->setHelperSet($helperSet->reveal());

        $definition = $command->getDefinition();
        self::assertTrue($definition->hasOption('directory'));

        $option = $definition->getOption('directory');
        self::assertEquals('directory', $option->getName());
        self::assertEquals('d', $option->getShortcut());
        self::assertTrue($option->isValueRequired());
        self::assertEquals(getcwd(), $option->getDefault());
    }

    public function testItValidatesInputDirectoryCorrectly()
    {
        $globalProphecy = $this->globalProphet->prophesize('DepDoc\\Command');

        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $input->getOption('directory')->willReturn('/test/dir')->shouldBeCalled();

        $globalProphecy->realpath('/test/dir')->willReturn(true)->shouldBeCalled();

        $output->getVerbosity()->willReturn(OutputInterface::VERBOSITY_NORMAL)->shouldBeCalled();
        $output->isVerbose()->willReturn(false)->shouldBeCalled();
        $output
            ->getFormatter()
            ->willReturn($this->getFormatter()->reveal())
            ->shouldBeCalled();

        $globalProphecy->reveal();

        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble(
            'test',
            $this->prophesize(ComposerPackageManager::class)->reveal(),
            $this->prophesize(NodePackageManager::class)->reveal(),
            $this->prophesize(ConfigurationService::class)->reveal()
        );

        $command->setHelperSet($helperSet->reveal());
        $result = $command->runExecute($input->reveal(), $output->reveal());

        self::assertEquals(0, $result);

        $this->globalProphet->checkPredictions();
    }

    public function testItVerboseOutputsTargetDirectory()
    {
        $prophecy = $this->globalProphet->prophesize('DepDoc\\Command');

        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $input->getOption('directory')->willReturn('/test/dir')->shouldBeCalled();

        $prophecy->realpath('/test/dir')->willReturn(true)->shouldBeCalled();

        $output->getVerbosity()->willReturn(OutputInterface::VERBOSITY_VERBOSE)->shouldBeCalled();
        $output->isVerbose()->willReturn(true)->shouldBeCalled();
        $output
            ->getFormatter()
            ->willReturn($this->getFormatter()->reveal())
            ->shouldBeCalled();
        $output->writeln('<info>Target directory:</info> /test/dir', 1)->shouldBeCalled();

        $prophecy->reveal();

        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble(
            'test',
            $this->prophesize(ComposerPackageManager::class)->reveal(),
            $this->prophesize(NodePackageManager::class)->reveal(),
            $this->prophesize(ConfigurationService::class)->reveal()
        );

        $command->setHelperSet($helperSet->reveal());

        $result = $command->runExecute($input->reveal(), $output->reveal());

        self::assertEquals(0, $result);

        $this->globalProphet->checkPredictions();
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
            ->willReturn($this->getFormatter()->reveal())
            ->shouldBeCalled();
        $output->write("\n")->shouldBeCalled();
        $output->writeln(Argument::containingString('<fg=white;bg=red> [ERROR] Invalid target directory given: '),
            1)->shouldBeCalled();

        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble(
            'test',
            $this->prophesize(ComposerPackageManager::class)->reveal(),
            $this->prophesize(NodePackageManager::class)->reveal(),
            $this->prophesize(ConfigurationService::class)->reveal()
        );

        $command->setHelperSet($helperSet->reveal());

        $result = $command->runExecute($input->reveal(), $output->reveal());

        self::assertEquals(-1, $result);
    }

    public function testItStopsOnInvalidDirectoryOption()
    {
        $globalProphecy = $this->globalProphet->prophesize('DepDoc\\Command');

        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $input->getOption('directory')->willReturn('/test/dir')->shouldBeCalled();

        $globalProphecy->realpath('/test/dir')->willReturn(false)->shouldBeCalled();

        $output->getVerbosity()->willReturn(OutputInterface::VERBOSITY_NORMAL)->shouldBeCalled();
        $output->isDecorated()->willReturn(false)->shouldBeCalled();
        $output
            ->getFormatter()
            ->willReturn($this->getFormatter()->reveal())
            ->shouldBeCalled();
        $output->write("\n")->shouldBeCalled();
        $output->writeln(Argument::containingString('<fg=white;bg=red> [ERROR] Invalid target directory given: '),
            1)->shouldBeCalled();

        $globalProphecy->reveal();

        $helperSet = $this->prophesize(HelperSet::class);

        $command = new BaseCommandTestDouble(
            'test',
            $this->prophesize(ComposerPackageManager::class)->reveal(),
            $this->prophesize(NodePackageManager::class)->reveal(),
            $this->prophesize(ConfigurationService::class)->reveal()
        );

        $command->setHelperSet($helperSet->reveal());

        $result = $command->runExecute($input->reveal(), $output->reveal());

        self::assertEquals(-1, $result);
    }

    public function testItCombinesAllInstalledPackages()
    {
        $targetDirectory = '/some/dir';

        $composerManager = $this->prophesize(ComposerPackageManager::class);
        $nodeManager = $this->prophesize(NodePackageManager::class);
        $configurationService = $this->prophesize(ConfigurationService::class);
        $composerPackageList = $this->prophesize(PackageManagerPackageList::class);
        $nodePackageList = $this->prophesize(PackageManagerPackageList::class);
        $configuration = $this->prophesize(ApplicationConfiguration::class);

        $configuration->isComposer()->willReturn(true)->shouldBeCalled();
        $configuration->isNpm()->willReturn(true)->shouldBeCalled();

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
            'test',
            $composerManager->reveal(),
            $nodeManager->reveal(),
            $configurationService->reveal()
        );

        $command->setConfiguration($configuration->reveal());

        $packages = $command->testGetInstalledPackages($targetDirectory);
        self::assertTrue($packages->has('Composer', 't1'));
        self::assertTrue($packages->has('Composer', 't2'));
        self::assertTrue($packages->has('Composer', 't3'));
        self::assertTrue($packages->has('Node', 't1'));
        self::assertTrue($packages->has('Node', 't2'));
        self::assertTrue($packages->has('Node', 't3'));
        self::assertCount(6, $packages->getAllFlat());
    }

    public function testItReadsOnlyConfiguredManagers()
    {
        $targetDirectory = '/some/dir';

        $composerManager = $this->prophesize(ComposerPackageManager::class);
        $nodeManager = $this->prophesize(NodePackageManager::class);
        $configurationService = $this->prophesize(ConfigurationService::class);
        $configuration = $this->prophesize(ApplicationConfiguration::class);

        $configuration->isComposer()->willReturn(false)->shouldBeCalled();
        $configuration->isNpm()->willReturn(false)->shouldBeCalled();

        $composerManager->getInstalledPackages($targetDirectory)->shouldNotBeCalled();
        $nodeManager->getInstalledPackages($targetDirectory)->shouldNotBeCalled();

        $command = new BaseCommandTestDouble(
            'test',
            $composerManager->reveal(),
            $nodeManager->reveal(),
            $configurationService->reveal()
        );

        $command->setConfiguration($configuration->reveal());

        $packages = $command->testGetInstalledPackages($targetDirectory);
        $this->assertCount(0, $packages->getAllFlat(), 'no packages should be read');
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

    /**
     * @return OutputFormatterInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    protected function getFormatter() {
        $formatter = $this->prophesize(OutputFormatterInterface::class);

        $formatter->isDecorated()->willReturn(false);
        $formatter->setDecorated(false)->willReturn(null);
        $formatter->format(Argument::type('string'))->willReturn(null);

        return $formatter;
    }
}
