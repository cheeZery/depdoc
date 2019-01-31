<?php

declare(strict_types=1);

namespace DepDocTest\Validator;

use DepDoc\Validator\StrictMode;
use PHPUnit\Framework\TestCase;

class StrictModeTest extends TestCase
{
    public function testLockedOnly(): void
    {
        $strictMode = StrictMode::lockedOnly();

        $this->assertTrue(
            $strictMode->isLockedOnly(),
            'strict mode should be locked only'
        );
    }
    public function testExistingOrLocked(): void
    {
        $strictMode = StrictMode::existingOrLocked();

        $this->assertTrue(
            $strictMode->isExistingOrLocked(),
            'strict mode should be existing or locked'
        );
    }

    public function testMajorAndMinor(): void
    {
        $strictMode = StrictMode::majorAndMinor();

        $this->assertTrue(
            $strictMode->isMajorAndMinor(),
            'strict mode should be major and minor'
        );
    }

    public function testFullSemanticVersioningMatch(): void
    {
        $strictMode = StrictMode::fullSemVerMatch();

        $this->assertTrue(
            $strictMode->isFullSemVerMatch(),
            'strict mode should be full semantic versioning match'
        );
    }
}
