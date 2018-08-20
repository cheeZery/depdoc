<?php

namespace DepDoc\Validator\Result;

interface ErrorResultInterface
{
    /**
     * @return string
     */
    public function getPackageManagerName(): string;

    /**
     * @return string
     */
    public function getPackageName(): string;

    /**
     * @return string
     */
    public function toString(): string;
}
