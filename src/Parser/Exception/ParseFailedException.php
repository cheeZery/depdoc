<?php

declare(strict_types=1);

namespace DepDoc\Parser\Exception;

/**
 * @codeCoverageIgnore
 */
class ParseFailedException extends \Exception
{
    public function __construct(string $filepath)
    {
        parent::__construct(sprintf('File could not be parsed: %s', $filepath));
    }
}
