<?php
declare(strict_types=1);

namespace DepDoc\Validator;

class Validator
{
    public function compare(array $installedPackages, array $documentedDependencies): void
    {
        $errors = [];

        foreach ($installedPackages as $packageManagerName => $packageManagerNameInstalledPackages) {
            $packageManagerNameDocumentedDependencies = $documentedDependencies[$packageManagerName] ?? [];

            foreach ($packageManagerNameInstalledPackages as $installedPackage) {
                $packageName = $installedPackage['name'];
                $installedVersion = $installedPackage['version'];

                if (!array_key_exists($packageName, $packageManagerNameDocumentedDependencies)) {
                    $errors[] = "$packageManagerName package $packageName is missing documentation!";
                    continue;
                }

                $documentedDependency = $packageManagerNameDocumentedDependencies[$packageName];

                $lockedVersion = $documentedDependency["lockedVersion"];

                if ($lockedVersion && $lockedVersion !== $installedVersion) {
                    $errors[] = "$packageManagerName package $packageName is installed in version $installedVersion, but locked for $lockedVersion!";
                    continue;
                }
            }
        }

        foreach ($documentedDependencies as $packageManagerName => $packageManagerNameDocumentedDependencies) {

            foreach ($packageManagerNameDocumentedDependencies as $documentedDependency) {
                $packageName = $documentedDependency['name'];

                $packageManagerNameInstalledPackages = $installedPackages[$packageManagerName] ?? [];

                if (!array_key_exists($packageName, $packageManagerNameInstalledPackages)) {
                    $errors[] = "$packageManagerName package $packageName is documented but not installed!";
                    continue;
                }
            }
        }

        if (count($errors) === 0) {
            return;
        }

        foreach ($errors as $error) {
            echo $error . PHP_EOL;
        }

        exit(1);
    }
}
