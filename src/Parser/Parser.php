<?php
declare(strict_types=1);

namespace DepDoc\Parser;

abstract class Parser
{
    abstract public function getDocumentedDependencies(?string $packageManagerName = null): ?array;
}
