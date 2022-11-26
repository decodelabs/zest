<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Terminus;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Manifest;

class Build implements Task
{
    public function execute(): bool
    {
        Zest::checkPackage();

        Terminus::info('Building assets');
        Terminus::newLine();

        if (!Zest::$package->runScript('build')) {
            return false;
        }

        Terminus::newLine();
        $this->buildManifest();
        return true;
    }

    protected function buildManifest(): void
    {
        $file = Zest::$package->rootDir->getDir(
            Zest::$config->getOutDir() ?? 'dist'
        )->getFile('manifest.json');

        Manifest::generateProduction($file, Zest::$config);
    }
}
