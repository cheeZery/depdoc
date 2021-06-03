<?php
declare(strict_types=1);

namespace DepDoc\Writer;

abstract class Writer
{
    abstract public function createDocumentation(array $installedPackages, array $documentedDependencies);
}
