<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Atlas\File;
use DecodeLabs\Commandment\Argument;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Terminus\Session;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Config;

trait ViteTrait
{
    #[Argument\Value(
        name: 'confName',
        description: 'Config name',
    )]
    #[Argument\Option(
        name: 'config',
        description: 'Config name',
        default: 'vite',
    )]
    public function __construct(
        protected Session $io
    ) {
    }

    protected function getConfigFileName(
        Request $request
    ): ?string {
        $output = $request->parameters->getAsString('confName') ??
            $request->parameters->getAsString('config') ??
            'vite';

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
        Request $request,
        ?string $configName,
        bool $passthrough = false
    ): array {
        $args = [];

        if ($configName !== null) {
            $args[] = $this->getConfigArgument($configName, $passthrough);
        }

        if ($request->parameters->getAsBool('emptyOutDir')) {
            $args[] = '--emptyOutDir';
        }

        return $args;
    }

    protected function getManifestFile(
        Config $config
    ): File {
        $dir = Zest::$project->rootDir->getDir(
            $config->outDir
        );

        $file = $dir->getFile($config->manifestName);

        if (!$file->exists()) {
            $file = $dir->getFile('.vite/' . $config->manifestName);
        }

        return $file;
    }
}
