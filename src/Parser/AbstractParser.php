<?php

namespace DepDoc\Parser;

abstract class AbstractParser
{
    abstract public function getDocumentedDependencies(?string $packageManagerName = null): ?array;
}
