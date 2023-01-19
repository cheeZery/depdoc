<?php

declare(strict_types=1);

namespace DepDocTest\Validator\Formatter;

use DepDoc\Validator\Formatter\JUnitFormatter;
use DepDoc\Validator\Result\ErrorDocumentedButNotInstalledResult;
use DepDoc\Validator\Result\ErrorVersionMismatchResult;
use PHPUnit\Framework\TestCase;

class JUnitFormatterTest extends TestCase
{
    public function testNoErrors(): void
    {
        $formatter = new JUnitFormatter();

        self::assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?><testsuite failures="0" name="depdoc-validate" tests="0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/junit-team/junit5/r5.5.1/platform-tests/src/test/resources/jenkins-junit.xsd"></testsuite>',
            $formatter->format([])
        );
    }

    public function testWithErrors(): void
    {
        $formatter = new JUnitFormatter();

        self::assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?><testsuite failures="2" name="depdoc-validate" tests="2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/junit-team/junit5/r5.5.1/platform-tests/src/test/resources/jenkins-junit.xsd"><testcase name="Composer"><failure type="ERROR" message="[Composer] package cheezery/depdoc is documented but not installed!" /></testcase><testcase name="Npm"><failure type="ERROR" message="[Npm] package cheezery/depdoc installed in version 1.1.0 but locked for 1.0.0!" /></testcase></testsuite>',
            $formatter->format([
                new ErrorDocumentedButNotInstalledResult('Composer', 'cheezery/depdoc'),
                new ErrorVersionMismatchResult('Npm', 'cheezery/depdoc', '1.1.0', '1.0.0'),
            ])
        );
    }
}
