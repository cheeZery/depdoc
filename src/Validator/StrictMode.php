<?php

declare(strict_types=1);

namespace DepDoc\Validator;

class StrictMode
{
    protected const EXISTING_OR_LOCKED = 0;
    protected const MAJOR_AND_MINOR = 1;
    protected const FULL_SEM_VER_MATCH = 2;

    /** @var int */
    private $mode;

    /**
     * @param int $mode
     */
    protected function __construct(int $mode)
    {
        $this->mode = $mode;
    }

    public static function existingOrLocked(): self
    {
        return new StrictMode(self::EXISTING_OR_LOCKED);
    }

    public static function majorAndMinor(): self
    {
        return new StrictMode(self::MAJOR_AND_MINOR);
    }

    public static function fullSemVerMatch(): self
    {
        return new StrictMode(self::FULL_SEM_VER_MATCH);
    }

    public function isExistingOrLocked(): bool
    {
        return $this->mode === self::EXISTING_OR_LOCKED;
    }

    public function isMajorAndMinor(): bool
    {
        return $this->mode === self::MAJOR_AND_MINOR;
    }

    public function isFullSemVerMatch(): bool
    {
        return $this->mode === self::FULL_SEM_VER_MATCH;
    }
}
