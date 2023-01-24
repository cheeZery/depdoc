<?php

declare(strict_types=1);

namespace DepDocTest\Validator\Formatter;

use DepDoc\Validator\Formatter\DefaultFormatter;
use DepDoc\Validator\Result\ErrorDocumentedButNotInstalledResult;
use PHPUnit\Framework\TestCase;

class DefaultFormatterTest extends TestCase
{
    public function testNoErrors(): void
    {
        $formatter = new DefaultFormatter();

        self::assertEquals(
            'Validation result: empty, all fine.',
            $formatter->format([])
        );
    }

    public function testWithErrors(): void
    {
        $formatter = new DefaultFormatter();

        self::assertEquals(
            "Validation result: found 1 error(s)\n[Composer] package cheezery/depdoc is documented but not installed!",
            $formatter->format([
                new ErrorDocumentedButNotInstalledResult('Composer', 'cheezery/depdoc')
           ])
        );
    }
}
