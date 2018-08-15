<?php

namespace DepDoc\Writer;

use DepDoc\Dependencies\DependencyList;

abstract class AbstractWriter
{
    abstract public function createDocumentation(
        string $filepath,
        array $installedPackages,
        DependencyList $documentedDependencies
    );
}
