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

class GenerateBuildManifest implements Task
{
    use ViteTrait;

    public function execute(): bool
    {
        Zest::checkPackage();

        $configName = $this->getConfigFileName();
        $config = Zest::loadConfig($configName);

        Cli::info('Generating build Zest manifest');

        Manifest::generateProduction(
            $this->getManifestFile($config),
            $config
        );

        return true;
    }
}
