<?php

namespace DepDocTest\Dependencies;

use DepDoc\Dependencies\DependencyData;
use DepDoc\Dependencies\DependencyDataAdditionalContent;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DependencyDataTest extends TestCase
{
    use ProphecyTrait;

    public function testItTakesConstructorDefaultValues()
    {
        $dependency = new DependencyData('manager', 'name', 'version', null);
        self::assertNull($dependency->getLockSymbol());
        self::assertInstanceOf(DependencyDataAdditionalContent::class, $dependency->getAdditionalContent());
        self::assertEquals([], $dependency->getAdditionalContent()->getAll());
    }

    public function testItStoresAdditionContent()
    {
        $dependency = new DependencyData('manager', 'name', 'version', null, [1, 2, 3]);
        self::assertEquals([1, 2, 3], $dependency->getAdditionalContent()->getAll());
    }

    public function testItKnowsItLockedIfLockSymbolProvided()
    {
        $dependency = new DependencyData('manager', 'name', 'version', 'symbol');
        self::assertTrue($dependency->isVersionLocked());

        $dependency = new DependencyData('manager', 'name', 'version', null);
        self::assertFalse($dependency->isVersionLocked());
    }
}
