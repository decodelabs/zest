<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task\GeneratePackageConfig;

use DecodeLabs\Terminus as Cli;
use DecodeLabs\Zest\Template;

class PackageTemplate extends Template
{
    public const FILE = __DIR__ . '/package.template';

    protected function generateSlot(
        string $name
    ): ?string {
        switch ($name) {
            case 'pkgName':
                return Cli::ask('What is your full package name?', function () {
                    $name = $this->controller->package->rootDir->getName();
                    return $this->controller->package->rootDir->getParent()?->getName() . '-' . $name;
                });
        }

        return parent::generateSlot($name);
    }
}
