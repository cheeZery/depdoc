<?php

declare(strict_types=1);

namespace DepDoc\Validator\Formatter;

use DepDoc\Validator\Result\ErrorResultInterface;

class JUnitFormatter implements FormatterInterface
{
    /**
     * @param ErrorResultInterface[] $validationResults
     *
     * @return string
     */
    public function format(array $validationResults): string
    {
        $result = '<?xml version="1.0" encoding="UTF-8"?>';

        $totalFailuresCount = count($validationResults);
        $totalTestsCount = count($validationResults);

        $groupedResults = $this->groupByPackageManager($validationResults);

        $result .= sprintf(
            '<testsuite failures="%d" name="depdoc-validate" tests="%d" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/junit-team/junit5/r5.5.1/platform-tests/src/test/resources/jenkins-junit.xsd">',
            $totalFailuresCount,
            $totalTestsCount
        );

        foreach ($groupedResults as $packageManager => $errorResults) {
            $result = sprintf('<testcase name="%s">', $this->escape($packageManager));
            foreach ($errorResults as $errorResult) {
                $result .= sprintf('<failure type="%s" message="%s" />', 'ERROR', $this->escape($errorResult->toString()));
            }
            $result .= '</testcase>';
        }

        $result .= '</testsuite>';

        return $result;
    }

    /**
     * @param ErrorResultInterface[] $validationResults
     *
     * @return array<string, ErrorResultInterface[]>
     */
    private function groupByPackageManager(array $validationResults): array
    {
        return array_reduce(
            $validationResults,
            function (array $groupedResults, ErrorResultInterface $errorResult): array {
                if (! array_key_exists($errorResult->getPackageManagerName(), $groupedResults)) {
                    $groupedResults[$errorResult->getPackageManagerName()] = [];
                }

                $groupedResults[$errorResult->getPackageManagerName()][] = $errorResult;

                return $groupedResults;
            },
            []
        );
    }

    /**
     * Escapes values for using in XML
     */
    private function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
