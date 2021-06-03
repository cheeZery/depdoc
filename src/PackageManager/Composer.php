<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

class Composer extends PackageManager
{
    public function getInstalledPackages(): array
    {
        $dependencies = $this->cliCommandHelper
            ->runAndGetOutputAsJson('composer show --direct --format json', $this->getName());

        $installedPackages = $dependencies["installed"] ?? [];

        $output = [];

        foreach ($installedPackages as $installedPackage) {
            // TODO: Create model for installed package
            $output[$installedPackage['name']] = $installedPackage;
        }

        return $output;
    }
}
