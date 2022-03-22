<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

use DepDoc\PackageManager\Exception\FailedToParseDependencyInformationException;
use DepDoc\PackageManager\Package\NodePackage;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use JsonException;

class NodePackageManager implements PackageManagerInterface
{
    public function getInstalledPackages(string $directory): PackageManagerPackageList
    {
        $packageList = new PackageManagerPackageList();

        // @TODO: Support npm binary detection
        $npmCommand = implode(' ', [
            'npm list',
            '--json',
            // Saves some performance
            '--depth 0',
            // Required to get description field
            '--long',
        ]);
        $command = sprintf(
            "cd %s && %s 2> /dev/null",
            escapeshellarg($directory),
            $npmCommand,
        );
        $output = shell_exec($command);

        if (!is_string($output)) {
            return $packageList;
        }

        $output = trim($output);

        if ($output === '' || $output[0] !== '{') {
            return $packageList;
        }

        try {
            $dependencies = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new FailedToParseDependencyInformationException(
                $this->getName(),
                $exception->getCode(),
                $exception->getMessage()
            );
        }

        $installedPackages = $dependencies['dependencies'] ?? [];

        $relevantData = array_flip(['name', 'path', 'version', 'description', 'peerMissing', 'extraneous']);

        array_walk($installedPackages, static function (&$dependency) use ($relevantData): void {
            $dependency = array_intersect_key($dependency, $relevantData);
        });

        foreach ($installedPackages as $installedPackage) {
            if (!$this->isValidPackage($installedPackage)) {
                continue;
            }

            $packageList->add(new NodePackage(
                $this->getName(),
                $installedPackage['name'],
                $installedPackage['version'],
                $installedPackage['description'] ?? $this->determinePackageDescription($installedPackage['path']) ?? null
          ));
        }

        return $packageList;
    }

    /**
     * @param array{peerMissing?: array<mixed>, extraneous?: bool} $installedPackage
     * @return bool
     */
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

    public function getName(): string
    {
        return 'Node';
    }

    /**
     * "npm list" for npm >= 8 does not return the description field in a reliable way.
     * It may be present or it may be missing. In case "npm list" doesn't return it, we'll
     * look into the package's own package.json, parse it and try to fetch the description
     * from there.
     */
    private function determinePackageDescription(string $packagePath): ?string
    {
        $packageJsonPath = $packagePath . DIRECTORY_SEPARATOR . 'package.json';
        $packageJsonContents = file_get_contents($packageJsonPath);

        if ($packageJsonContents === false) {
            return null;
        }

        try {
            $packageJson = json_decode($packageJsonContents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            return null;
        }

        return $packageJson['description'] ?? null;
    }
}
