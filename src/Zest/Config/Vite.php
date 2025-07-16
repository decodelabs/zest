<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Config;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\File;
use DecodeLabs\Coercion;
use DecodeLabs\Collections\Tree;
use DecodeLabs\Exceptional;
use DecodeLabs\Iota;
use DecodeLabs\Overpass\Project;
use DecodeLabs\Zest\Config;
use DecodeLabs\Zest\Controller;

class Vite implements Config
{
    public const array Extensions = ['ts', 'mjs', 'cjs', 'js'];

    public string $path {
        get => $this->controller->project->rootDir->path;
    }

    public protected(set) ?string $host = null;
    public protected(set) ?int $port = null;
    public protected(set) ?bool $https = false;
    public protected(set) string $outDir = 'dist';
    public protected(set) string $assetsDir = 'assets';
    public protected(set) string $publicDir = 'public';
    public protected(set) array $aliases = [];
    public protected(set) ?string $urlPrefix = null;
    public protected(set) ?string $entry = null;
    public protected(set) string $manifestName = 'manifest.json';

    protected File $file;
    protected Controller $controller;

    public function __construct(
        Controller $controller,
        ?string $configName = null
    ) {
        $this->controller = $controller;

        $name = 'vite.';

        if ($configName !== null) {
            $name .= $configName . '.';
        }

        $name .= 'config';

        foreach (self::Extensions as $ext) {
            $this->file = $controller->project->rootDir->getFile($name . '.' . $ext);

            if ($this->file->exists()) {
                break;
            }
        }

        $this->reload();
    }

    /**
     * Reload local data
     */
    public function reload(): void
    {
        if (!$this->file->exists()) {
            return;
        }

        if ($this->loadPhpConfig()) {
            return;
        }

        $nodeModules = $this->file->getParent()?->getDir('node_modules');

        if (!$nodeModules?->exists()) {
            throw Exceptional::Runtime(
                message: 'Node modules not found. Please run `npm install`'
            );
        }

        $loaderFile = $nodeModules->getFile('.decodelabs-zest/load-vite-config.cjs');
        $srcLoaderFile = Atlas::file(__DIR__ . '/load-vite-config.cjs');

        if ($loaderFile->exists()) {
            $hash = $loaderFile->getHash('crc32');

            if ($hash !== $srcLoaderFile->getHash('crc32')) {
                $loaderFile->delete();
            }
        }

        if (!$loaderFile->exists()) {
            $loaderFile->putContents(
                file_get_contents(__DIR__ . '/load-vite-config.cjs')
            );
        }

        $json = new Project()->bridge($loaderFile, (string)$this->file);

        // @phpstan-ignore-next-line
        $tree = new Tree(Coercion::asArray(
            Coercion::asArray($json)['config'] ?? []
        ));

        $this->host = $tree->server->host->as('?string') ?? 'localhost';
        $this->port = $tree->server->port->as('int');
        $this->https = $tree->server->https->as('?bool') ?? false;
        $this->outDir = $tree->build->outDir->as('?string') ?? 'dist';
        $this->assetsDir = $tree->build->assetsDir->as('?string') ?? 'assets';
        $this->publicDir = $tree->publicDir->as('?string') ?? 'public';
        // @phpstan-ignore-next-line
        $this->aliases = $tree->resolve->alias->as('string[]');
        $this->urlPrefix = $tree->base->as('?string') ?? '/';
        $this->entry = $tree->build->rollupOptions->input->as('?string') ?? 'src/main.' . $this->getDefaultExtension();

        $manifest = $tree->build['manifest'] ?? true;

        if (is_string($manifest)) {
            $this->manifestName = $manifest;
        } else {
            $this->manifestName = 'manifest.json';
        }
    }

    protected function loadPhpConfig(): bool
    {
        $iota = Iota::loadStatic('zest');

        $filename = preg_replace(
            '/\.(' . implode('|', self::Extensions) . ')$/',
            '.php',
            $this->file->name
        );

        if (
            !$filename ||
            !$iota->has((string)$filename)
        ) {
            return false;
        }



        $config = $iota->returnAsType($filename, Generic::class);

        $this->host = $config->host ?? 'localhost';
        $this->port = $config->port;
        $this->https = $config->https ?? false;
        $this->outDir = $config->outDir;
        $this->assetsDir = $config->assetsDir;
        $this->publicDir = $config->publicDir;
        $this->aliases = $config->aliases;
        $this->urlPrefix = $config->urlPrefix;
        $this->entry = $config->entry ?? 'src/main.' . $this->getDefaultExtension();
        $this->manifestName = $config->manifestName;

        return true;
    }


    public function loadDefaults(): void
    {
        $this->host = 'localhost';
        $this->port = rand(3000, 9999);
        $this->outDir = 'dist';
        $this->assetsDir = 'assets';
        $this->publicDir = 'public';
        $this->urlPrefix = null;
        $this->entry = 'src/main.' . $this->getDefaultExtension();
        $this->manifestName = 'manifest.json';
    }


    private function getDefaultExtension(): string
    {
        if (str_ends_with(basename($this->file->path), '.ts')) {
            return 'ts';
        } else {
            return 'js';
        }
    }
}
