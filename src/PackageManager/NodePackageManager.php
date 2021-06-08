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

        if ($output === null || $output === false) {
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

        $relevantData = array_flip(['name', 'version', 'description', 'peerMissing', 'extraneous']);

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
                $installedPackage['description'] ?? null
            ));
        }

        return $packageList;
    }

    /**
     * @param array{peerMissing?: array, extraneous?: bool} $installedPackage
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
}
