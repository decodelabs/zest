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

class GenerateDevManifest implements Task
{
    use ViteTrait;

    public function execute(): bool
    {
        Zest::checkProject();

        $configName = $this->getConfigFileName();
        $config = Zest::loadConfig($configName);

        Cli::info('Generating dev Zest manifest');

        Manifest::generateDev(
            $this->getManifestFile($config),
            $config
        );

        return true;
    }
}
