<?php

namespace DepDoc\Writer;

abstract class AbstractWriter
{
    abstract public function createDocumentation(string $filepath, array $installedPackages, array $documentedDependencies);
}
