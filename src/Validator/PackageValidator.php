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

        foreach ($installedPackages->getAllFlat() as $package) {
            if ($dependencyList->has($package->getManagerName(), $package->getName()) === false) {
                $errors[] = new ErrorMissingDocumentationResult($package->getManagerName(), $package->getName());
                continue;
            }

            /** @var DependencyData $dependency */
            $dependency = $dependencyList->get($package->getManagerName(), $package->getName());

            if ($dependency->isVersionLocked() && $dependency->getVersion() !== $package->getVersion()) {
                $errors[] = new ErrorVersionMissMatchResult(
                    $package->getManagerName(),
                    $package->getName(),
                    $package->getVersion(),
                    $dependency->getVersion()
                );
                continue;
            }
        }

        foreach ($dependencyList->getAllFlat() as $dependency) {
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
