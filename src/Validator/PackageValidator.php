<?php

namespace DepDoc\Validator;

class PackageValidator
{
    /**
     * @param array $installedPackages
     * @param array $documentedDependencies
     * @return string[]
     */
    public function compare(array $installedPackages, array $documentedDependencies): array
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

        return $errors;
    }
}
