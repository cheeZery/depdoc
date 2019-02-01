<?php
declare(strict_types=1);

namespace DepDoc\Validator;

use DepDoc\Dependencies\DependencyData;
use DepDoc\PackageManager\Package\PackageManagerPackageInterface;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;
use DepDoc\Validator\Result\ErrorDocumentedButNotInstalledResult;
use DepDoc\Validator\Result\ErrorMissingDocumentationResult;
use DepDoc\Validator\Result\ErrorResultInterface;
use DepDoc\Validator\Result\ErrorVersionMismatchResult;
use vierbergenlars\SemVer\version;

class PackageValidator
{
    /**
     * @param StrictMode                $mode
     * @param PackageManagerPackageList $installedPackages
     * @param PackageManagerPackageList $dependencyList
     *
     * @return ErrorResultInterface[]
     */
    public function compare(
        StrictMode $mode,
        PackageManagerPackageList $installedPackages,
        PackageManagerPackageList $dependencyList
    ): array {
        $errors = [];

        foreach ($installedPackages->getAllFlat() as $package) {
            if ($dependencyList->has($package->getManagerName(), $package->getName()) === false) {

                if (!$mode->isLockedOnly()) {
                    $errors[] = new ErrorMissingDocumentationResult($package->getManagerName(), $package->getName());
                }
                continue;
            }

            /** @var DependencyData $dependency */
            $dependency = $dependencyList->get($package->getManagerName(), $package->getName());

            if (!$this->doesVersionMatch($mode, $dependency, $package)) {
                $errors[] = new ErrorVersionMisMatchResult(
                    $package->getManagerName(),
                    $package->getName(),
                    $package->getVersion(),
                    $dependency->getVersion()
                );
                continue;
            }
        }

        if ($mode->isLockedOnly()) {
            return $errors;
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

    /**
     * @param StrictMode                     $mode
     * @param DependencyData                 $dependency
     * @param PackageManagerPackageInterface $package
     *
     * @return bool
     */
    protected function doesVersionMatch(
        StrictMode $mode,
        DependencyData $dependency,
        PackageManagerPackageInterface $package
    ): bool {
        if ($mode->isLockedOnly() || $mode->isExistingOrLocked()) {
            if ($dependency->isVersionLocked()) {
                return $dependency->getVersion() === $package->getVersion();
            }

            return true;
        }

        if ($mode->isMajorAndMinor()) {
            $dependencyVersion = new version($dependency->getVersion());
            $packageVersion = new version($package->getVersion());

            return (
                $dependencyVersion->getMajor() === $packageVersion->getMajor()
                && $dependencyVersion->getMinor() === $packageVersion->getMinor()
            );
        }

        return $dependency->getVersion() === $package->getVersion();
    }
}
