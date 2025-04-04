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
        Cli::$command
            ->addArgument('?confName', 'Config name')
            ->addArgument('-config=vite', 'Config name');

        $output = Coercion::asString(
            Cli::$command['confName'] ??
                Cli::$command['config'] ??
                'vite'
        );

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

    /**
     * @return array<string>
     */
    protected function getBuildArguments(
        ?string $configName,
        bool $passthrough = false
    ): array {
        $args = [];

        if ($configName !== null) {
            $args[] = $this->getConfigArgument($configName, $passthrough);
        }

        if (Cli::$command['emptyOutDir']) {
            $args[] = '--emptyOutDir';
        }

        return $args;
    }

    protected function getManifestFile(
        Config $config
    ): File {
        $dir = Zest::$package->rootDir->getDir(
            $config->outDir
        );

        $file = $dir->getFile($config->manifestName);

        if (!$file->exists()) {
            $file = $dir->getFile('.vite/' . $config->manifestName);
        }

        return $file;
    }
}
