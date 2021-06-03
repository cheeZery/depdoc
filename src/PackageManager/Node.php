<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

class Node extends PackageManager
{
    public function getInstalledPackages(): array
    {
        $command = implode(' ', [
            'npm list',
            '--json',
            // Saves some performance
            '--depth 0',
            // Required to get description field
            '--long',
        ]);
        $dependencies = $this->cliCommandHelper
            ->runAndGetOutputAsJson($command, $this->getName());

        if (count($dependencies) === 0) {
            return [];
        }

        $installedPackages = array_merge(
            $dependencies["dependencies"] ?? [],
            $dependencies['devDependencies'] ?? []
        );

        $relevantData = array_flip(["name", "version", "description", "peerMissing"]);

        array_walk($installedPackages, function (&$dependency) use ($relevantData) {
            $dependency = array_intersect_key($dependency, $relevantData);
        });

        $requiredPackages = [];
        foreach ($installedPackages as $installedPackage) {
            if ($this->isPeerDependency($installedPackage)) {
                continue;
            }

            $requiredPackages[$installedPackage['name']] = $installedPackage;
        }

        return $requiredPackages;
    }

    private function isPeerDependency(array $installedPackage): bool
    {
        return array_key_exists('peerMissing', $installedPackage) && count($installedPackage['peerMissing']) > 0;
    }
}
