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
        $output = shell_exec("cd " . escapeshellarg($directory) . " && " . $npmCommand . " 2> /dev/null");

        if ($output === null) {
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

        $relevantData = array_flip(['name', 'version', 'description']);

        array_walk($installedPackages, function (&$dependency) use ($relevantData) {
            $dependency = array_intersect_key($dependency, $relevantData);
        });

        foreach ($installedPackages as $installedPackage) {
            $packageList->add(new NodePackage(
                $this->getName(),
                $installedPackage['name'],
                $installedPackage['version'],
                $installedPackage['description'] ?? null
            ));
        }

        return $packageList;
    }

    public function getName(): string
    {
        return 'Node';
    }
}
