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

        // The dependencies key contains both, dev & non-dev
        $installedPackages = $dependencies["dependencies"];

        $relevantData = array_flip(["name", "version", "description", "peerMissing", "extraneous"]);

        array_walk($installedPackages, function (&$dependency) use ($relevantData) {
            $dependency = array_intersect_key($dependency, $relevantData);
        });

        $requiredPackages = [];
        foreach ($installedPackages as $installedPackage) {
            if (!$this->isValidPackage($installedPackage)) {
                continue;
            }

            $requiredPackages[$installedPackage['name']] = $installedPackage;
        }

        return $requiredPackages;
    }

    private function isValidPackage(array $installedPackage): bool
    {
        // NPM <= 6 peer dependency note
        if (array_key_exists('peerMissing', $installedPackage) && count($installedPackage['peerMissing']) > 0) {
            return false;
        }

        // NPM 7 extraneous (installed but unneeded) package
        if (array_key_exists('extraneous', $installedPackage) && $installedPackage['extraneous'] === true) {
            return false;
        }

        return true;
    }
}
