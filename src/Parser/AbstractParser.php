<?php

namespace DepDoc\Parser;

abstract class AbstractParser
{
    abstract public function getDocumentedDependencies(string $filepath, ?string $packageManagerName = null): ?array;
}
