<?php
declare(strict_types=1);

namespace DepDocTest;

use DepDoc\Application;
use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\NodePackageManager;
use DepDoc\Parser\MarkdownParser;
use DepDoc\Validator\PackageValidator;
use DepDoc\Writer\MarkdownWriter;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class ApplicationTest extends TestCase
{
    /** @var PHPProphet */
    protected $prophet;

    protected function setUp()
    {
        $this->prophet = new PHPProphet();
    }

    public function testUpdateAction()
    {
        $composerPackages = [
            'dep1' => [
                'name' => 'dep1',
                'version' => '1.0.0',
                'description' => 'Test dep 1',
            ],
            'dep2' => [
                'name' => 'dep2',
                'version' => '2.0.0',
                'description' => 'Test dep 2',
            ],
        ];

        $prophecyDepDoc = $this->prophet->prophesize('DepDoc');
        $composerManager = $this->prophesize(ComposerPackageManager::class);
        $nodeManager = $this->prophesize(NodePackageManager::class);
        $parser = $this->prophesize(MarkdownParser::class);
        $writer = $this->prophesize(MarkdownWriter::class);

        $prophecyDepDoc
            ->file_exists(Argument::containingString('tests/DEPENDENCIES.md'))
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        $composerManager
            ->getName()
            ->shouldBeCalledTimes(1)
            ->willReturn('composer');
        $composerManager
            ->getInstalledPackages()
            ->shouldBeCalledTimes(1)
            ->willReturn($composerPackages);

        $nodeManager
            ->getName()
            ->shouldBeCalledTimes(1)
            ->willReturn('node');
        $nodeManager
            ->getInstalledPackages()
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        /** @noinspection PhpStrictTypeCheckingInspection */
        $parser
            ->getDocumentedDependencies(Argument::any())
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        $writer
            ->createDocumentation([
                'composer' => $composerPackages,
                'node' => [],
            ], [])
            ->shouldBeCalledTimes(1);

        $prophecyDepDoc->reveal();

        $application = new Application();
        $application
            ->setManagerComposer($composerManager->reveal())
            ->setManagerNode($nodeManager->reveal())
            ->setWriter($writer->reveal())
            ->setParser($parser->reveal());
        $result = $application->updateAction([
            'targetDirectory' => realpath(__DIR__ . DIRECTORY_SEPARATOR),
        ]);

        $this->assertTrue($result);

        $this->prophet->checkPredictions();
    }

    public function testUpdateValidate()
    {
        $composerPackages = [
            'dep1' => [
                'name' => 'dep1',
                'version' => '1.0.0',
                'description' => 'Test dep 1',
            ],
        ];

        $prophecyDepDoc = $this->prophet->prophesize('DepDoc');
        $composerManager = $this->prophesize(ComposerPackageManager::class);
        $nodeManager = $this->prophesize(NodePackageManager::class);
        $parser = $this->prophesize(MarkdownParser::class);
        $validator = $this->prophesize(PackageValidator::class);

        $prophecyDepDoc
            ->file_exists(Argument::containingString('tests/DEPENDENCIES.md'))
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        $composerManager
            ->getName()
            ->shouldBeCalledTimes(1)
            ->willReturn('composer');
        $composerManager
            ->getInstalledPackages()
            ->shouldBeCalledTimes(1)
            ->willReturn($composerPackages);

        $nodeManager
            ->getName()
            ->shouldBeCalledTimes(1)
            ->willReturn('node');
        $nodeManager
            ->getInstalledPackages()
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        /** @noinspection PhpStrictTypeCheckingInspection */
        $parser
            ->getDocumentedDependencies(Argument::any())
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        $validator
            ->compare([
                'composer' => $composerPackages,
                'node' => [],
            ], [])
            ->willReturn([]);

        $prophecyDepDoc->reveal();

        $application = new Application();
        $application
            ->setManagerComposer($composerManager->reveal())
            ->setManagerNode($nodeManager->reveal())
            ->setValidator($validator->reveal())
            ->setParser($parser->reveal());
        $result = $application->validateAction([
            'targetDirectory' => realpath(__DIR__ . DIRECTORY_SEPARATOR),
        ]);

        $this->assertTrue($result);

        $this->prophet->checkPredictions();
    }

}
