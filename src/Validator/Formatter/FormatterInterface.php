<?php

declare(strict_types=1);

namespace DepDoc\Validator\Formatter;

use DepDoc\Validator\Result\ErrorResultInterface;

interface FormatterInterface
{
    /**
     * @param ErrorResultInterface[] $validationResults
     *
     * @return string
     */
    public function format(array $validationResults): string;
}
