<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action\GeneratePackageConfig;

use DecodeLabs\Zest;
use DecodeLabs\Zest\Template;

class PackageTemplate extends Template
{
    protected const string File = __DIR__ . '/package.template';

    protected function generateSlot(
        string $name
    ): ?string {
        $io = Zest::getIoSession();

        switch ($name) {
            case 'pkgName':
                return $io->ask('What is your full package name?', function () {
                    $name = $this->controller->project->rootDir->name;
                    return $this->controller->project->rootDir->getParent()?->name . '-' . $name;
                });
        }

        return parent::generateSlot($name);
    }
}
