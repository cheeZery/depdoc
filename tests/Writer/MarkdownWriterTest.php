<?php

namespace DepDocTest\Writer;

use DepDoc\Dependencies\DependencyData;
use DepDoc\Dependencies\DependencyDataAdditionalContent;
use DepDoc\PackageManager\Package\ComposerPackage;
use DepDoc\PackageManager\Package\NodePackage;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use DepDoc\Writer\MarkdownWriter;
use DepDoc\Writer\WriterConfiguration;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class MarkdownWriterTest extends TestCase
{
    use ProphecyTrait;

    protected PHPProphet $globalProphet;

    protected function setUp(): void
    {
        $this->globalProphet = new PHPProphet();
    }

    public function testItSuccessfullyCreatesFileData(): void
    {
        $filepath = '/some/file';

        $globalProphecy = $this->globalProphet->prophesize('DepDoc\\Writer');

        $installedPackages = $this->prophesize(PackageManagerPackageList::class);
        $dependencyList = $this->prophesize(PackageManagerPackageList::class);
        $configuration = $this->prophesize(WriterConfiguration::class);

        $installedPackages->getAll()->willReturn([
            'Composer' => [
                $this->getComposerPackageProphecy('t1p1', '1.0.0', null),
                $this->getComposerPackageProphecy('t1p2', '2.0.0', 'Awesome package!'),
                $this->getComposerPackageProphecy('t1p3', '3.2.1', 'Locked version'),
                $this->getComposerPackageProphecy('t1p4', '4.0.0', 'With content'),
            ],
            'Node' => [
                $this->getNodePackageProphecy('t2p1', '1.0.0', 'Awesome package #2!'),
                $this->getNodePackageProphecy('t2p2', '1.0.0', ''),
            ],
        ])->shouldBeCalled();

        $lockedDependency = $this->getDependencyDataProphecy(true);
        $lockedDependency->getLockSymbol()->willReturn('!')->shouldBeCalled();

        $lockedDependencyWithContent = $this->getDependencyDataProphecy(true, ['line 1  ', '  ', 'line 3']);
        $lockedDependencyWithContent->getLockSymbol()->willReturn('!')->shouldBeCalled();

        $dependencyList->get('Composer', 't1p1')->willReturn(null)->shouldBeCalled();
        $dependencyList->get('Composer', 't1p2')->willReturn(
            $this->getDependencyDataProphecy(false)->reveal()
        )->shouldBeCalled();
        $dependencyList->get('Composer', 't1p3')->willReturn(
            $lockedDependency->reveal()
        )->shouldBeCalled();
        $dependencyList->get('Composer', 't1p4')->willReturn(
            $lockedDependencyWithContent->reveal()
        )->shouldBeCalled();
        $dependencyList->get('Node', 't2p1')->willReturn(
            $this->getDependencyDataProphecy(false)->reveal()
        )->shouldBeCalled();
        $dependencyList->get('Node', 't2p2')->willReturn(
            $this->getDependencyDataProphecy(false)->reveal()
        )->shouldBeCalled();

        $configuration->isExportExternalLink()->willReturn(true)->shouldBeCalledTimes(6);
        $configuration->getNewline()->willReturn('#nl')->shouldBeCalledTimes(24);

        $globalProphecy->file_put_contents($filepath, [
            '# Composer#nl',
            '#nl',
            '## t1p1 `1.0.0` [link](https://packagist.org/packages/t1p1)#nl',
            '#nl',
            '## t1p2 `2.0.0` [link](https://packagist.org/packages/t1p2)#nl',
            '> Awesome package!#nl',
            '#nl',
            '## t1p3 `3.2.1` ! [link](https://packagist.org/packages/t1p3)#nl',
            '> Locked version#nl',
            '#nl',
            '## t1p4 `4.0.0` ! [link](https://packagist.org/packages/t1p4)#nl',
            '> With content#nl',
            'line 1  #nl',
            '  #nl',
            'line 3#nl',
            '#nl',
            '# Node#nl',
            '#nl',
            '## t2p1 `1.0.0` [link](https://www.npmjs.com/package/t2p1)#nl',
            '> Awesome package #2!#nl',
            '#nl',
            '## t2p2 `1.0.0` [link](https://www.npmjs.com/package/t2p2)#nl',
            '#nl',
            '#nl',
        ], LOCK_EX)->shouldBeCalled();

        $globalProphecy->reveal();

        $writer = new MarkdownWriter($configuration->reveal());
        $writer->createDocumentation(
            $filepath,
            $installedPackages->reveal(),
            $dependencyList->reveal()
        );

        $this->globalProphet->checkPredictions();
    }

    protected function getComposerPackageProphecy(string $name, string $version, ?string $description)
    {
        $package = $this->prophesize(ComposerPackage::class);

        $package->getName()->willReturn($name)->shouldBeCalled();
        $package->getVersion()->willReturn($version)->shouldBeCalled();
        $package->getDescription()->willReturn($description)->shouldBeCalled();
        $package->getExternalLink()->willReturn('https://packagist.org/packages/' . $name)->shouldBeCalled();

        return $package;
    }

    protected function getNodePackageProphecy(string $name, string $version, ?string $description)
    {
        $package = $this->prophesize(NodePackage::class);

        $package->getName()->willReturn($name)->shouldBeCalled();
        $package->getVersion()->willReturn($version)->shouldBeCalled();
        $package->getDescription()->willReturn($description)->shouldBeCalled();
        $package->getExternalLink()->willReturn('https://www.npmjs.com/package/' . $name)->shouldBeCalled();

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
