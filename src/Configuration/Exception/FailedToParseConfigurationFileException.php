<?php
declare(strict_types=1);

namespace DepDoc\Configuration\Exception;

/**
 * @codeCoverageIgnore
 */
class FailedToParseConfigurationFileException extends \Exception
{
    public function __construct(string $filepath, string $message)
    {
        parent::__construct(sprintf(
            'Failed to parse configuration file at %s: %s',
            $filepath,
            $message
        ));
    }
}
