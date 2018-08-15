<?php

namespace DepDoc\Validator;

use DepDoc\Validator\Result\BaseResult;
use DepDoc\Validator\Result\ErrorDocumentedButNotInstalled;
use DepDoc\Validator\Result\ErrorMissingDocumentation;
use DepDoc\Validator\Result\ErrorVersionMissMatch;

class PackageValidator
{
    /**
     * @param array $installedPackages
     * @param array $documentedDependencies
     * @return BaseResult[]
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
                    $errors[] = new ErrorMissingDocumentation($packageManagerName, $packageName);
                    continue;
                }

                $documentedDependency = $packageManagerNameDocumentedDependencies[$packageName];

                $lockedVersion = $documentedDependency['lockedVersion'];

                if ($lockedVersion && $lockedVersion !== $installedVersion) {
                    $errors[] = new ErrorVersionMissMatch($packageManagerName, $packageName, $installedVersion,
                        $lockedVersion);
                    continue;
                }
            }
        }

        foreach ($documentedDependencies as $packageManagerName => $packageManagerNameDocumentedDependencies) {

            foreach ($packageManagerNameDocumentedDependencies as $documentedDependency) {
                $packageName = $documentedDependency['name'];

                $packageManagerNameInstalledPackages = $installedPackages[$packageManagerName] ?? [];

                if (!array_key_exists($packageName, $packageManagerNameInstalledPackages)) {
                    $errors[] = new ErrorDocumentedButNotInstalled($packageManagerName, $packageName);
                    continue;
                }
            }
        }

        return $errors;
    }
}
