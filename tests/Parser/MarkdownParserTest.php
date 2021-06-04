<?php

namespace DepDocTest\Parser;

use DepDoc\Dependencies\DependencyData;
use DepDoc\Parser\Exception\ParseFailedException;
use DepDoc\Parser\MarkdownParser;
use phpmock\Mock;
use phpmock\prophecy\PHPProphet;
use PHPUnit\Framework\TestCase;

class MarkdownParserTest extends TestCase
{
    /** @var PHPProphet */
    protected $prophet;

    protected function setUp(): void
    {
        $this->prophet = new PHPProphet();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mock::disableAll();
    }

    public function testItParsesDependenciesFileCorrectlyWithoutFilter()
    {
        $filepath = '/some/file';
        $prophecy = $this->prophet->prophesize('DepDoc\\Parser');

        $prophecy
            ->file_exists($filepath)
            ->willReturn(true)
            ->shouldBeCalled();
        $prophecy
            ->file($filepath)
            ->willReturn(explode(PHP_EOL, $this->getValidDependenciesFileData()))
            ->shouldBeCalled();

        $prophecy->reveal();

        $parser = new MarkdownParser();
        $packageList = $parser->getDocumentedDependencies($filepath, null);

        $packages = $packageList->getAllFlat();
        $this->assertCount(8, $packages);

        /** @var DependencyData $package */
        $package = $packageList->get('Composer', 'php-mock/php-mock-prophecy');
        $this->assertNotNull($package);
        $this->assertCount(2, $package->getAdditionalContent()->getAll());
        $this->assertEquals(['', 'working'], array_values($package->getAdditionalContent()->getAll()));

        $package = $packageList->get('Composer', 'symfony/console');
        $this->assertNotNull($package);
        $this->assertCount(3, $package->getAdditionalContent()->getAll());
        $this->assertEquals(['', 'test 1  ', 'test 2'], array_values($package->getAdditionalContent()->getAll()));

        $package = $packageList->get('Composer', 'symfony/yaml');
        $this->assertNotNull($package);
        $this->assertCount(4, $package->getAdditionalContent()->getAll());
        $this->assertEquals(['', 'Will leave only one', '', 'consecutive empty line'], array_values($package->getAdditionalContent()->getAll()));
    }

    public function getValidDependenciesFileData(): string
    {
        return <<<DATA
# Composer

## php-mock/php-mock-prophecy `0.0.2` [link](https://packagist.org/packages/php-mock/php-mock-prophecy)
> Mock built-in PHP functions (e.g. time()) with Prophecy. This package relies on PHP's namespace fallback policy. No further extension is needed.

working

## phpunit/phpunit `7.3.2` [link](https://packagist.org/packages/phpunit/phpunit)
> The PHP Unit Testing framework.

amazing!

## symfony/console `v4.1.3` [link](https://packagist.org/packages/symfony/console)
> Symfony Console Component

test 1  
test 2

## symfony/property-access `v4.1.3` [link](https://packagist.org/packages/symfony/property-access)
> Symfony PropertyAccess Component

## symfony/serializer `v4.1.3` [link](https://packagist.org/packages/symfony/serializer)
> Symfony Serializer Component

## symfony/var-dumper `v4.1.3` [link](https://packagist.org/packages/symfony/var-dumper)
> Symfony mechanism for exploring and dumping PHP variables

## symfony/yaml `v4.1.3` [link](https://packagist.org/packages/symfony/yaml)
> Symfony Yaml Component

Will leave only one



consecutive empty line

## zendframework/zend-servicemanager `3.3.2` [link](https://packagist.org/packages/zendframework/zend-servicemanager)
> Factory-Driven Dependency Injection Container

DATA;

    }

    public function testItThrowsExceptionIfLinesCouldNotBeRead()
    {
        $this->expectException(ParseFailedException::class);

        $filepath = '/some/file';
        $prophecy = $this->prophet->prophesize('DepDoc\\Parser');

        $prophecy
            ->file_exists($filepath)
            ->willReturn(true)
            ->shouldBeCalled();
        $prophecy
            ->file($filepath)
            ->willReturn(false)
            ->shouldBeCalled();

        $prophecy->reveal();

        $parser = new MarkdownParser();
        $parser->getDocumentedDependencies($filepath, null);
    }
}
