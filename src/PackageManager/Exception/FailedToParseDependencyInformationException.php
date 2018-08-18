<?php
declare(strict_types=1);

namespace DepDoc\PackageManager\Exception;

class FailedToParseDependencyInformationException extends \Exception
{
    /**
     * @param string $name
     * @param int $jsonErrorCode
     * @param string $jsonErrorMessage
     */
    public function __construct(string $name, int $jsonErrorCode, string $jsonErrorMessage)
    {
        parent::__construct(sprintf(
            'Error occurred while trying to read %s dependencies. %s: %s' . PHP_EOL,
            $name,
            $jsonErrorCode,
            $jsonErrorMessage
        ));
    }
}
