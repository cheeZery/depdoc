<?php
declare(strict_types=1);

namespace DepDoc\Parser\Exception;

class MissingFileException extends \Exception
{
    public function __construct($filepath)
    {
        parent::__construct(sprintf('File not found: %s', $filepath));
    }
}
