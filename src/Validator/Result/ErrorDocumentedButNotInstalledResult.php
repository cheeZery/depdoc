<?php

namespace DepDoc\Validator\Result;

class ErrorDocumentedButNotInstalledResult extends AbstractErrorResult
{
    /**
     * @return string
     */
    public function toString(): string
    {
        return sprintf(
            "[%s] package %s is documented but not installed!",
            $this->getPackageManagerName(),
            $this->getPackageName()
        );
    }
}
