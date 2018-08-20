<?php

namespace DepDocTest\Dependencies;

use DepDoc\Dependencies\DependencyData;
use DepDoc\Dependencies\DependencyDataAdditionalContent;
use PHPUnit\Framework\TestCase;

class DependencyDataTest extends TestCase
{
    public function testItTakesConstructorDefaultValues()
    {
        $dependency = new DependencyData('manager', 'name', 'version', null);
        $this->assertNull($dependency->getLockSymbol());
        $this->assertInstanceOf(DependencyDataAdditionalContent::class, $dependency->getAdditionalContent());
        $this->assertEquals([], $dependency->getAdditionalContent()->getAll());
    }

    public function testItStoresAdditionContent()
    {
        $dependency = new DependencyData('manager', 'name', 'version', null, [1, 2, 3]);
        $this->assertEquals([1, 2, 3], $dependency->getAdditionalContent()->getAll());
    }

    public function testItKnowsItLockedIfLockSymbolProvided()
    {
        $dependency = new DependencyData('manager', 'name', 'version', 'symbol');
        $this->assertTrue($dependency->isVersionLocked());

        $dependency = new DependencyData('manager', 'name', 'version', null);
        $this->assertFalse($dependency->isVersionLocked());
    }
}
