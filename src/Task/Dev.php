<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
declare(ticks=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Terminus;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Manifest;

class Dev implements Task
{
    public function execute(): bool
    {
        Zest::checkPackage();

        $this->buildManifest();

        Zest::$package->runServerScript('dev');
        Terminus::newLine();

        return Zest::run('build');
    }

    protected function buildManifest(): void
    {
        $file = Zest::$package->rootDir->getDir(
            Zest::$config->getOutDir() ?? 'dist'
        )->getFile('manifest.json');

        Manifest::generateDev($file, Zest::$config);
    }
}
