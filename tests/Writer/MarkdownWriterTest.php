<?php

namespace DepDocTest\Writer;

use DepDoc\Dependencies\DependencyData;
use DepDoc\Dependencies\DependencyDataAdditionalContent;
use DepDoc\PackageManager\Package\ComposerPackage;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use DepDoc\Writer\MarkdownWriter;
use DepDoc\Writer\WriterConfiguration;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class MarkdownWriterTest extends TestCase
{
    /** @var PHPProphet */
    protected $prophet;

    protected function setUp()
    {
        $this->prophet = new PHPProphet();
    }

    public function testItSuccessfullyCreatesFileData()
    {
        $filepath = '/some/file';

        $prophecy = $this->prophet->prophesize('DepDoc\\Writer');

        $installedPackages = $this->prophesize(PackageManagerPackageList::class);
        $dependencyList = $this->prophesize(PackageManagerPackageList::class);
        $configuration = $this->prophesize(WriterConfiguration::class);

        $installedPackages->getAll()->willReturn([
            'Test 1' => [
                $this->getComposerPackageProphecy('t1p1', '1.0.0', null),
                $this->getComposerPackageProphecy('t1p2', '2.0.0', 'Awesome package!'),
                $this->getComposerPackageProphecy('t1p3', '3.2.1', 'Locked version'),
                $this->getComposerPackageProphecy('t1p4', '4.0.0', 'With content'),
            ],
            'Test 2' => [
                $this->getComposerPackageProphecy('t2p1', '1.0.0', 'Awesome package #2!'),
                $this->getComposerPackageProphecy('t2p2', '1.0.0', null),
            ],
        ])->shouldBeCalled();

        $lockedDependency = $this->getDependencyDataProphecy(true);
        $lockedDependency->getLockSymbol()->willReturn('!')->shouldBeCalled();

        $lockedDependencyWithContent = $this->getDependencyDataProphecy(true, ['line 1', 'line 2']);
        $lockedDependencyWithContent->getLockSymbol()->willReturn('!')->shouldBeCalled();

        $dependencyList->get('Test 1', 't1p1')->willReturn(null)->shouldBeCalled();
        $dependencyList->get('Test 1', 't1p2')->willReturn(
            $this->getDependencyDataProphecy(false)->reveal()
        )->shouldBeCalled();
        $dependencyList->get('Test 1', 't1p3')->willReturn(
            $lockedDependency->reveal()
        )->shouldBeCalled();
        $dependencyList->get('Test 1', 't1p4')->willReturn(
            $lockedDependencyWithContent->reveal()
        )->shouldBeCalled();
        $dependencyList->get('Test 2', 't2p1')->willReturn(
            $this->getDependencyDataProphecy(false)->reveal()
        )->shouldBeCalled();
        $dependencyList->get('Test 2', 't2p2')->willReturn(
            $this->getDependencyDataProphecy(false)->reveal()
        )->shouldBeCalled();

        $configuration->getNewline()->willReturn('#nl')->shouldBeCalledTimes(25);

        $prophecy->fopen($filepath, 'w')->willReturn('file-handle')->shouldBeCalled();
        $prophecy->fwrite('file-handle', Argument::that(function (string $line) {
            // @TODO: Verify call count or use virtual fs to compare raw file content
            $lines = [
                '### Test 1#nl' => 1,
                '### Test 2#nl' => 1,
                '#nl' => 1 + 4 + 2 + 1,
                '##### t1p1 `1.0.0`#nl' => 1,
                '##### t1p2 `2.0.0`#nl' => 1,
                '##### t1p3 `3.2.1` !#nl' => 1,
                '##### t1p4 `4.0.0` !#nl' => 1,
                '##### t2p1 `1.0.0`#nl' => 1,
                '##### t2p2 `1.0.0`#nl' => 1,
                '> #nl' => 2,
                '> Awesome package!#nl' => 1,
                '> Awesome package #2!#nl' => 1,
                '> Locked version#nl' => 1,
                '> With content#nl' => 1,
                'line 1#nl' => 1,
                'line 2#nl' => 1,
            ];

            return isset($lines[$line]);
        }))->shouldBeCalled();
        $prophecy->fclose('file-handle')->shouldBeCalled();
        $prophecy->reveal();

        $writer = new MarkdownWriter();
        $writer->createDocumentation(
            $filepath,
            $installedPackages->reveal(),
            $dependencyList->reveal(),
            $configuration->reveal()
        );

        $this->prophet->checkPredictions();
    }

    protected function getComposerPackageProphecy(string $name, string $version, ?string $description)
    {
        $package = $this->prophesize(ComposerPackage::class);

        $package->getName()->willReturn($name)->shouldBeCalled();
        $package->getVersion()->willReturn($version)->shouldBeCalled();
        $package->getDescription()->willReturn($description)->shouldBeCalled();

        return $package;
    }

    protected function getDependencyDataProphecy(?bool $isVersionLocked = null, array $additionalContent = [])
    {
        $package = $this->prophesize(DependencyData::class);
        $packageContent = $this->prophesize(DependencyDataAdditionalContent::class);

        $packageContent->getAll()->willReturn($additionalContent);

        if ($isVersionLocked !== null) {
            $package->isVersionLocked()->willReturn($isVersionLocked);
        }
        $package->getAdditionalContent()->willReturn($packageContent->reveal());

        return $package;
    }
}
