<?php

namespace DepDoc\Parser;

abstract class Parser
{
    abstract public function getDocumentedDependencies(?string $packageManagerName = null): ?array;
}