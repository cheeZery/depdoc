<?php

namespace DepDoc\Parser;

use DepDoc\Dependencies\DependencyList;

abstract class AbstractParser
{
    abstract public function getDocumentedDependencies(
        string $filepath,
        ?string $packageManagerName = null
    ): DependencyList;
}
