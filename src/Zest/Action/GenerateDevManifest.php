<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

declare(ticks=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Manifest;

class GenerateDevManifest implements Action
{
    use ViteTrait;

    public function execute(
        Request $request
    ): bool {
        Zest::checkProject();

        $configName = $this->getConfigFileName($request);
        $config = Zest::loadConfig($configName);

        $this->io->info('Generating dev Zest manifest');

        Manifest::generateDev(
            $this->getManifestFile($config),
            $config
        );

        return true;
    }
}
