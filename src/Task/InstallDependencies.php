<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Overpass;
use DecodeLabs\Zest;

class InstallDependencies implements Task
{
    public const DEV_PACKAGES = [
        'vite' => '^3.2'
    ];

    public function execute(): bool
    {
        $packages = $devPackages = [];

        foreach (static::DEV_PACKAGES as $name => $version) {
            $devPackages[$name] = Overpass::preparePackageInstallName($name, $version);
        }

        foreach (Zest::$config->getPlugins() as $key => $name) {
            $plugin = Zest::getPlugin($name);

            foreach ($plugin->getPackages() as $name => $version) {
                $packages[$name] = Overpass::preparePackageInstallName($name, $version);
            }

            foreach ($plugin->getDevPackages() as $name => $version) {
                $devPackages[$name] = Overpass::preparePackageInstallName($name, $version);
            }
        }

        Overpass::install(...array_values($packages));
        Overpass::installDev(...array_values($devPackages));

        return true;
    }
}
