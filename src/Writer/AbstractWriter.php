<?php

namespace DepDoc\Writer;

abstract class AbstractWriter
{
    abstract public function createDocumentation(array $installedPackages, array $documentedDependencies);
}
