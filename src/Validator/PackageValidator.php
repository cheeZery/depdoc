<?php

namespace DepDoc\Validator;

use DepDoc\Dependencies\DependencyList;
use DepDoc\Validator\Result\AbstractErrorResult;
use DepDoc\Validator\Result\ErrorDocumentedButNotInstalled;
use DepDoc\Validator\Result\ErrorMissingDocumentation;
use DepDoc\Validator\Result\ErrorVersionMissMatch;

class PackageValidator
{
    /**
     * @param array $installedPackages
     * @param DependencyList $dependencyList
     * @return AbstractErrorResult[]
     */
    public function compare(array $installedPackages, DependencyList $dependencyList): array
    {
        $errors = [];

        foreach ($installedPackages as $packageManagerName => $packageManagerNameInstalledPackages) {
            foreach ($packageManagerNameInstalledPackages as $installedPackage) {
                $packageName = $installedPackage['name'];
                $installedVersion = $installedPackage['version'];

                if ($dependencyList->has($packageManagerName, $packageName) === false) {
                    $errors[] = new ErrorMissingDocumentation($packageManagerName, $packageName);
                    continue;
                }

                $dependency = $dependencyList->get($packageManagerName, $packageName);

                if ($dependency->isVersionLocked() && $dependency->getVersion() !== $installedVersion) {
                    $errors[] = new ErrorVersionMissMatch(
                        $packageManagerName,
                        $packageName,
                        $installedVersion,
                        $dependency->getVersion()
                    );
                    continue;
                }
            }
        }

        foreach ($dependencyList as $dependency) {
            $packageManagerNameInstalledPackages = $installedPackages[$dependency->getPackageManagerName()] ?? [];

            if (!array_key_exists($dependency->getPackageName(), $packageManagerNameInstalledPackages)) {
                $errors[] = new ErrorDocumentedButNotInstalled(
                    $dependency->getPackageManagerName(),
                    $dependency->getPackageName()
                );
                continue;
            }
        }

        return $errors;
    }
}
