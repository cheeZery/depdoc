<?php

namespace DepDoc\Validator\Result;

class ErrorMissingDocumentation extends AbstractErrorResult
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
