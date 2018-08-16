<?php
declare(strict_types=1);

namespace DepDocTest;

use DepDoc\Application;
use DepDoc\PackageManager\ComposerPackageManager;
use DepDoc\PackageManager\NodePackageManager;
use DepDoc\Parser\MarkdownParserInterface;
use DepDoc\Validator\PackageValidator;
use DepDoc\Writer\MarkdownWriterInterface;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;

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
        $targetDependenciesDirectory = '/test';
        $targetDependenciesFilepath = $targetDependenciesDirectory . DIRECTORY_SEPARATOR . 'DEPENDENCIES.md';
        $targetActionOptions = [
            'targetDirectory' => $targetDependenciesDirectory,
        ];

        $prophecyDepDoc = $this->prophet->prophesize('DepDoc');
        $composerManager = $this->prophesize(ComposerPackageManager::class);
        $nodeManager = $this->prophesize(NodePackageManager::class);
        $parser = $this->prophesize(MarkdownParserInterface::class);
        $writer = $this->prophesize(MarkdownWriterInterface::class);

        $prophecyDepDoc
            ->file_exists($targetDependenciesFilepath)
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        $composerManager
            ->getName()
            ->shouldBeCalledTimes(1)
            ->willReturn('composer');
        $composerManager
            ->getInstalledPackages($targetDependenciesDirectory)
            ->shouldBeCalledTimes(1)
            ->willReturn($composerPackages);

        $nodeManager
            ->getName()
            ->shouldBeCalledTimes(1)
            ->willReturn('node');
        $nodeManager
            ->getInstalledPackages($targetDependenciesDirectory)
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        $parser
            ->getDocumentedDependencies($targetDependenciesFilepath)
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
        $result = $application->updateAction($targetActionOptions);

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
        $targetDependenciesDirectory = '/test';
        $targetDependenciesFilepath = $targetDependenciesDirectory . DIRECTORY_SEPARATOR . 'DEPENDENCIES.md';
        $targetActionOptions = [
            'targetDirectory' => $targetDependenciesDirectory,
        ];

        $prophecyDepDoc = $this->prophet->prophesize('DepDoc');
        $composerManager = $this->prophesize(ComposerPackageManager::class);
        $nodeManager = $this->prophesize(NodePackageManager::class);
        $parser = $this->prophesize(MarkdownParserInterface::class);
        $validator = $this->prophesize(PackageValidator::class);

        $prophecyDepDoc
            ->file_exists($targetDependenciesFilepath)
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        $composerManager
            ->getName()
            ->shouldBeCalledTimes(1)
            ->willReturn('composer');
        $composerManager
            ->getInstalledPackages($targetDependenciesDirectory)
            ->shouldBeCalledTimes(1)
            ->willReturn($composerPackages);

        $nodeManager
            ->getName()
            ->shouldBeCalledTimes(1)
            ->willReturn('node');
        $nodeManager
            ->getInstalledPackages($targetDependenciesDirectory)
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        $parser
            ->getDocumentedDependencies($targetDependenciesFilepath)
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
        $result = $application->validateAction($targetActionOptions);

        $this->assertTrue($result);

        $this->prophet->checkPredictions();
    }

}
