<?php

namespace DepDoc\Validator;

use DepDoc\Dependencies\DependencyList;
use DepDoc\Validator\Result\AbstractErrorResult;
use DepDoc\Validator\Result\ErrorDocumentedButNotInstalledResult;
use DepDoc\Validator\Result\ErrorMissingDocumentationResult;
use DepDoc\Validator\Result\ErrorVersionMissMatchResult;

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
                    $errors[] = new ErrorMissingDocumentationResult($packageManagerName, $packageName);
                    continue;
                }

                $dependency = $dependencyList->get($packageManagerName, $packageName);

                if ($dependency->isVersionLocked() && $dependency->getVersion() !== $installedVersion) {
                    $errors[] = new ErrorVersionMissMatchResult(
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
                $errors[] = new ErrorDocumentedButNotInstalledResult(
                    $dependency->getPackageManagerName(),
                    $dependency->getPackageName()
                );
                continue;
            }
        }

        return $errors;
    }
}
