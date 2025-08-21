<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action\GeneratePackageConfig;

use DecodeLabs\Terminus\Session;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Template;

class PackageTemplate extends Template
{
    protected const string File = __DIR__ . '/package.template';

    public function __construct(
        protected Zest $zest,
        protected Session $io
    ) {
        parent::__construct();
    }

    protected function generateSlot(
        string $name
    ): ?string {
        switch ($name) {
            case 'pkgName':
                return $this->io->ask('What is your full package name?', function () {
                    $name = $this->zest->project->rootDir->name;
                    return $this->zest->project->rootDir->getParent()?->name . '-' . $name;
                });
        }

        return parent::generateSlot($name);
    }
}
