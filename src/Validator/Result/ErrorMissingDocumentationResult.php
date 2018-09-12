<?php
declare(strict_types=1);

namespace DepDoc\Validator\Result;

/**
 * @codeCoverageIgnore
 */
class ErrorMissingDocumentationResult extends AbstractErrorResult
{
    /**
     * @return string
     */
    public function toString(): string
    {
        return sprintf(
            "[%s] package %s is missing documentation!",
            $this->getPackageManagerName(),
            $this->getPackageName()
        );
    }
}
