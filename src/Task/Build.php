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
    use ViteTrait;

    public function execute(): bool
    {
        Zest::checkPackage();

        Terminus::info('Building assets');
        Terminus::newLine();

        $configName = $this->getConfigFileName();

        if (!Zest::$package->runNpx(
            'vite',
            'build',
            $this->getConfigArgument($configName)
        )) {
            return false;
        }

        Terminus::newLine();
        $config = Zest::loadConfig($configName);

        Manifest::generateProduction(
            $this->getManifestFile($config),
            $config
        );

        return true;
    }
}
