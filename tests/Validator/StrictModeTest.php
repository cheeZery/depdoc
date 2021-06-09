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

        self::assertTrue(
            $strictMode->isLockedOnly(),
            'strict mode should be locked only'
        );
    }
    public function testExistingOrLocked(): void
    {
        $strictMode = StrictMode::existingOrLocked();

        self::assertTrue(
            $strictMode->isExistingOrLocked(),
            'strict mode should be existing or locked'
        );
    }

    public function testMajorAndMinor(): void
    {
        $strictMode = StrictMode::majorAndMinor();

        self::assertTrue(
            $strictMode->isMajorAndMinor(),
            'strict mode should be major and minor'
        );
    }

    public function testFullSemanticVersioningMatch(): void
    {
        $strictMode = StrictMode::fullSemVerMatch();

        self::assertTrue(
            $strictMode->isFullSemVerMatch(),
            'strict mode should be full semantic versioning match'
        );
    }
}
