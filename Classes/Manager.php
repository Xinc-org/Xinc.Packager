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

namespace Xinc\Packager;

/**
 * Management of packages install/uninstall/update
 */
class Manager
{
    /** @type string directory to the composer binary */
    private $composerBinDir = '';

    /** @type string directory to the composer json file */
    private $composerJsonDir = '';

    /** @type string directory to the packager dir */
    private $packageDir = '';

    /**
     * Constructor
     *
     * @param string $composerBinDir Directory where composer executable can be found.
     * @param string $composerJsonDir Directory where composer.json can be found.
     * @param string $packageDir Directory where PackageStates.php can be found.
     */
    public function __construct($composerBinDir, $composerJsonDir, $packageDir)
    {
        $this->composerBinDir = $composerBinDir;
        $this->composerJsonDir = $composerJsonDir;
        $this->packageDir = $packageDir;
    }

    /**
     * Install a package with composer.
     *
     * @param string $name Name of package in composer!!
     * @return void
     * @TODO Can we have Real package name instead of composer ones?
     */
    public function install($name)
    {
        $bridge = $this->getBridge();
        if ($bridge->isInstalled($name)) {
            throw new Exception('Package "' . $name . '" already installed.');
        }
        throw new Exception('Not yet implemented. Use "composer require ' . $name . '"');
    }

    /**
     * Return versions of packages with composer.
     *
     * @return void
     */
    public function version()
    {
        $packages = $this->getBridge()->getPackages();
        $versions = array('composer' => \Composer\Composer::RELEASE_DATE);
        foreach ($packages as $package) {
            $versions[$package->getName()] = $package->getVersion();
        }
        return $versions;
    }

    /**
     * Remove a package with composer.
     *
     * @return void
     */
    public function deinstall($name)
    {
        $bridge = $this->getBridge();
        if (!$bridge->isInstalled($name)) {
            throw new Exception('Package "' . $name . '" is not installed installed.');
        }
        throw new Exception('Not yet implemented. Use "composer remove ' . $name . '"');
    }

    /**
     * Activates a package in PackageStates.php and composers ClassLoader.
     *
     * @return void
     * @TODO No dependency chack yet.
     */
    public function activate($name)
    {
        $statesManager = new StatesManager();
        $statesManager->startInstallMode();
        $package = $statesManager->getPackage($name);
        $package->setStateActive();
        $statesManager->stopInstallMode();
    }

    /**
     * Deactivates a package in PackageStates.php and composers ClassLoader.
     *
     * @return void
     * @TODO No dependency chack yet.
     */
    public function deactivate($name)
    {
        $statesManager = new StatesManager();
        $statesManager->startInstallMode();
        $package = $statesManager->getPackage($name);
        $package->setStateInactive();
        $statesManager->stopInstallMode();
    }

    public function getBridge()
    {
        return new Composer\Outside($this->composerBinDir, $this->composerJsonDir);
    }
}
