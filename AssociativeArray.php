<?php

require_once('DependencyException.php');

/**
 * Class AssociativeArray
 */
class AssociativeArray
{


    /**
     * @param array $packages
     * @throws ItselfDependency
     * @throws LoopDependencyException
     * @throws MissingJoinedDependencyInKey
     * @throws MissingDependencyInName
     * @throws MissingResembleNameException
     */
    public function validatePackageDefinitions(array $packages): void
    {
        $this->checkResemblanceName($packages);
        $this->checkJoinDependencies($packages);
        $this->checkJoinDependenciesKeyInArray($packages);
        $this->checkLoopDependencies($packages, []);
    }


    /**
     * @param array $packages
     * @throws MissingResembleNameException
     */
    private function checkResemblanceName(array $packages): void
    {
        foreach ($packages as $key => $value) {
            if ($key !== $value['name']) {
                throw new MissingResembleNameException("$value[name] is not identically $key");
            }
        }
    }
    /**
     * @param array $packages
     * @param string $packageName
     * @return array
     * @throws ItselfDependency
     * @throws LoopDependencyException
     * @throws MissingJoinedDependencyInKey
     * @throws MissingDependencyInName
     * @throws MissingResembleNameException
     */
    public function getAllPackageDependencies(array $packages, string $packageName): array
    {
        $this->validatePackageDefinitions($packages);
        $ArrayPackages = $this->getAllArrayOfDependencies($packages, $packageName);
        $ArrayPackages = $this->changeGlobalArrayForOne($ArrayPackages);

        return $ArrayPackages;
    }

    /**
     * @param array $packages
     * @throws MissingDependencyInName
     */
    private function checkJoinDependencies(array $packages): void
    {
        foreach ($packages as $key => $value) {
            if (array_key_exists("dependencies", $value) === false) {
                throw new MissingDependencyInName("$key not have dependencies");
            }
        }
    }


    /**
     * @param array $packages
     * @throws MissingJoinedDependencyInKey
     */
    private function checkJoinDependenciesKeyInArray(array $packages): void
    {
        foreach ($packages as $key => $value) {
            foreach ($value['dependencies'] as $keyDependencies => $valueDependencies) {
                if (!array_key_exists($valueDependencies, $packages)) {
                    throw new MissingJoinedDependencyInKey("$valueDependencies is not member array package $key");
                }
            }
        }
    }


    /**
     * @param array $packages
     * @param array $usedDependencies
     * @throws ItselfDependency
     * @throws LoopDependencyException
     */
    private function checkLoopDependencies(array $packages, array $usedDependencies): void
    {
        foreach ($packages as $key => $package) {

            $dependencies = $package['dependencies'];

            if (!empty($dependencies)) {
                if (in_array($package['name'], $dependencies)) {
                    throw new ItselfDependency('One of packages has dependency with itself');
                }

                $usedDependencies[] = $package['name'];

                foreach ($dependencies as $dependency) {
                    if (in_array($dependency, $usedDependencies)) {
                        throw new LoopDependencyException('Loop dependency');
                    }

                    $this->checkLoopDependencies($packages[$dependency], $usedDependencies);
                }
            }
        }
    }

    /**
     * @param array $packages
     * @param string $packageName
     * @return array
     */
    private function getAllArrayOfDependencies(array $packages, string $packageName): array
    {
        $fullArrayOfAllNeededPackages = [$packageName];
        foreach ($packages[$packageName]['dependencies'] as $key => $dependencie) {
            if (count($dependencie) !== 0) {
                $fullArrayOfAllNeededPackages[] = $this->getAllArrayOfDependencies($packages, $dependencie);
            }
        }

        return $fullArrayOfAllNeededPackages;
    }


    /**
     * @param array $multipleArrayForFormat
     * @return array
     */
    private function changeGlobalArrayForOne(array $multipleArrayForFormat): array
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($multipleArrayForFormat));
        $easyArray = iterator_to_array($iterator, false);
        $invertedArray = array_reverse($easyArray);
        $endDependencies = array_unique($invertedArray);

        return $endDependencies;
    }


}