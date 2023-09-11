<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Atlas\File;
use DecodeLabs\Coercion;
use DecodeLabs\Terminus as Cli;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Config;

trait ViteTrait
{
    protected function getConfigFileName(): ?string
    {
        Cli::getCommandDefinition()
            ->addArgument('-config=vite', 'Config name');

        Cli::prepareArguments();
        $output = Coercion::toString(Cli::getArgument('config'));

        if (
            $output === 'vite' ||
            $output === ''
        ) {
            $output = null;
        }

        return $output;
    }

    protected function getConfigArgument(
        ?string $name,
        bool $passthrough = false
    ): string {
        if ($name === null) {
            return '';
        }

        if ($passthrough) {
            return '--config=' . $name;
        }

        return '--config=vite.' . $name . '.config.js';
    }

    protected function getManifestFile(
        Config $config
    ): File {
        return Zest::$package->rootDir->getDir(
            $config->getOutDir() ?? 'dist'
        )->getFile($config->getManifestName());
    }
}
