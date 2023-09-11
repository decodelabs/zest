<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Overpass;

class InstallDependencies implements Task
{
    public const DEV_PACKAGES = [
        'vite' => '^4'
    ];

    public function execute(): bool
    {
        $packages = $devPackages = [];

        foreach (static::DEV_PACKAGES as $name => $version) {
            $devPackages[$name] = Overpass::preparePackageInstallName($name, $version);
        }

        Overpass::install(...array_values($packages));
        Overpass::installDev(...array_values($devPackages));

        return true;
    }
}
