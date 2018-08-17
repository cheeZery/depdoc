<?php

namespace DepDoc\Validator;

use DepDoc\Dependencies\DependencyData;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use DepDoc\Validator\Result\ErrorDocumentedButNotInstalledResult;
use DepDoc\Validator\Result\ErrorMissingDocumentationResult;
use DepDoc\Validator\Result\ErrorResultInterface;
use DepDoc\Validator\Result\ErrorVersionMissMatchResult;

class PackageValidator
{
    /**
     * @param PackageManagerPackageList $installedPackages
     * @param PackageManagerPackageList $dependencyList
     * @return ErrorResultInterface[]
     */
    public function compare(
        PackageManagerPackageList $installedPackages,
        PackageManagerPackageList $dependencyList
    ): array {
        $errors = [];

        foreach ($installedPackages as $packageManagerName => $packageManagerNameInstalledPackages) {
            foreach ($packageManagerNameInstalledPackages as $installedPackage) {
                $packageName = $installedPackage->getName();
                $installedVersion = $installedPackage->getVersion();

                if ($dependencyList->has($packageManagerName, $packageName) === false) {
                    $errors[] = new ErrorMissingDocumentationResult($packageManagerName, $packageName);
                    continue;
                }

                /** @var DependencyData $dependency */
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
            if ($installedPackages->has($dependency->getManagerName(), $dependency->getName()) === false) {
                $errors[] = new ErrorDocumentedButNotInstalledResult(
                    $dependency->getManagerName(),
                    $dependency->getName()
                );
                continue;
            }
        }

        return $errors;
    }
}
