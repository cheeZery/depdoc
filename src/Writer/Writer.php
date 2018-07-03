<?php

namespace DepDoc\Writer;

abstract class Writer
{
    abstract public function createDocumentation(array $installedPackages, array $documentedDependencies);
}