<?php
/**
 * Xinc - Cross integration and continous management.
 * This script belongs to the Xinc package "Xinc.Packager".
 *
 * It is free software; you can redistribute it and/or modify it under the terms of the
 * GNU Lesser General Public License, either version 3 of the License, or (at your option) any later version.
 *
 * @package Xinc.Packager
 * @author  Alexander Opitz <opitz.alexander@googlemail.com>
 * @license http://www.gnu.org/copyleft/lgpl.html GNU LGPL 3+
 * @see     https://github.com/Xinc-org/Xinc.Packager
 */

namespace Xinc\Packager\Composer;

/**
 * This class handles communication with composer from outside composer. This class manipulates composer handling.
 */
class Outside
{
    public function __construct($binDir, $jsonDir)
    {
        \Phar::loadPhar($binDir . '/composer.phar', 'composer.phar');
        require 'phar://composer.phar/src/bootstrap.php';

        $this->io = new \Composer\IO\NullIO();
        $this->composer = \Composer\Factory::create($this->io, $jsonDir . '/composer.json');

        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $jsonFile = new \Composer\Json\JsonFile($vendorDir . '/composer/installed.json');

        $this->composer->getRepositoryManager()->setLocalRepository(
            new \Composer\Repository\InstalledFilesystemRepository($jsonFile)
        );
    }

    /**
     * Tests if a package is already installed by composer.
     *
     * @param string $packageName Name of the package.
     * @return boolean True if a package was found in local repository otherwise false.
     */
    public function isInstalled($packageName)
    {
        if (count($this->composer->getRepositoryManager()->getLocalRepository()->findPackages($packageName))) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Returns the package if installed by composer.
     *
     * @param string $packageName Name of the package.
     * @return Composer\Package\PackageInterface Null if package was not found.
     */
    public function getPackage($packageName)
    {
        return $this->composer->getRepositoryManager()->getLocalRepository()->findPackage($packageName);
    }

    /**
     * Returns all installed composer packages.
     *
     * @return array Array of \Composer\Package\CompletePackage objects.
     */
    public function getPackages()
    {
        return $this->composer->getRepositoryManager()->getLocalRepository()->getPackages();
    }
}
