<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
declare(ticks=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Terminus as Cli;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Manifest;

class Dev implements Task
{
    use ViteTrait;

    public function execute(): bool
    {
        Zest::checkPackage();

        $configName = $this->getConfigFileName();
        $config = Zest::loadConfig($configName);

        Manifest::generateDev(
            $this->getManifestFile($config),
            $config
        );

        Zest::$package->runNpx(
            'vite',
            $this->getConfigArgument($configName)
        );

        Cli::newLine();

        return Zest::run(
            'build',
            $this->getConfigArgument($configName, true)
        );
    }
}
