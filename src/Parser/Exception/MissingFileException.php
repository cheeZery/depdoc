<?php
declare(strict_types=1);

namespace DepDoc\Parser\Exception;

/**
 * @codeCoverageIgnore
 */
class MissingFileException extends \Exception
{
    public function __construct(string $filepath)
    {
        parent::__construct(sprintf('File not found: %s', $filepath));
    }
}
