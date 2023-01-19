<?php

declare(strict_types=1);

namespace DepDoc\Validator\Formatter;

use DepDoc\Validator\Result\ErrorResultInterface;

class DefaultFormatter implements FormatterInterface
{
    /**
     * @param ErrorResultInterface[] $validationResults
     *
     * @return string
     */
    public function format(array $validationResults): string
    {
        if (count($validationResults) === 0) {
            return 'Validation result: empty, all fine.';
        }

        $result = sprintf(
            'Validation result: found %s error(s)' . PHP_EOL,
            count($validationResults)
        );

        $result .= implode(
            PHP_EOL,
            array_map(
                static fn (ErrorResultInterface $errorResult): string => $errorResult->toString(),
                $validationResults
            )
        );

        return $result;
    }
}
