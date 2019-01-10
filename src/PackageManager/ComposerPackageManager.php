<?php
declare(strict_types=1);

namespace DepDoc\PackageManager;

use Composer\Composer;
use Composer\Package\CompletePackage;
use Composer\Package\Link;
use DepDoc\PackageManager\Package\ComposerPackage;
use DepDoc\PackageManager\PackageList\PackageManagerPackageList;

class ComposerPackageManager implements PackageManagerInterface
{
    /**
     * @var Composer
     */
    protected $composer;

    public function __construct(Composer $composer)
    {
        $this->composer = $composer;
    }

    public function getInstalledPackages(string $directory): PackageManagerPackageList
    {
        $packageList = new PackageManagerPackageList();

        $requiredPackages = $this->loadCurrentRequirements();

        $lockedRepository = $this->composer->getLocker()->getLockedRepository(true);
        $localRepository = $this->composer->getRepositoryManager()->getLocalRepository();

        foreach ($requiredPackages as $package) {

            if ($package->getConstraint() === null) {
                continue; // Can't handle a package without a constraint.
            }

            $lockedPackage = $lockedRepository->findPackage($package->getTarget(), $package->getConstraint());
            $localPackage = $localRepository->findPackage($package->getTarget(), $package->getConstraint());

            if ($lockedPackage === null) {
                continue;
            }

            $packageList->add(
                new ComposerPackage(
                    $this->getName(),
                    $lockedPackage->getName(),
                    $lockedPackage->getPrettyVersion(),
                    $localPackage instanceof CompletePackage ? $localPackage->getDescription() : null
                )
            );
        }

        return $packageList;
    }

    public function getName(): string
    {
        return 'Composer';
    }

    /**
     * @return Link[]
     */
    protected function loadCurrentRequirements(): array
    {
        $package = $this->composer->getPackage();

        $requirements = [];

        /** @var Link[] $mergedRequirements */
        $mergedRequirements = array_merge($package->getRequires(), $package->getDevRequires());

        foreach ($mergedRequirements as $requirement) {
            $requirements[$requirement->getTarget()] = $requirement;
        }

        ksort($requirements);

        return $requirements;
    }
}
