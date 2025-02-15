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
use DecodeLabs\Overpass;
use DecodeLabs\Zest\Config;
use DecodeLabs\Zest\Controller;

class Generic implements Config
{
    protected ?string $host = null;
    protected ?int $port = null;
    protected ?bool $https = false;
    protected ?string $outDir = null;
    protected ?string $assetsDir = null;
    protected ?string $publicDir = null;
    protected ?string $urlPrefix = null;
    protected ?string $entry = null;
    protected string $manifestName = 'manifest.json';

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

        $name .= 'config.js';

        $this->file = $controller->package->rootDir->getFile($name);
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

        $json = Overpass::bridge($loaderFile, (string)$this->file);
        $tree = new Tree(Coercion::toArray(
            Coercion::toArray($json)['config'] ?? []
        ));

        $this->host = $tree->server->host->as('?string') ?? 'localhost';
        $this->port = $tree->server->port->as('int');
        $this->https = $tree->server->https->as('?bool') ?? false;
        $this->outDir = $tree->build->outDir->as('?string') ?? 'dist';
        $this->assetsDir = $tree->build->assetsDir->as('?string') ?? 'assets';
        $this->publicDir = $tree->publicDir->as('?string') ?? 'public';
        $this->urlPrefix = $tree->base->as('?string') ?? '/';
        $this->entry = $tree->build->rollupOptions->input->as('?string') ?? 'src/main.js';

        $manifest = $tree->build['manifest'] ?? true;

        if (is_string($manifest)) {
            $this->manifestName = $manifest;
        } else {
            $this->manifestName = 'manifest.json';
        }
    }


    public function loadDefaults(
        ?string $name = null
    ): void {
        switch ($name) {
            case 'df-r7':
                $this->host = 'localhost';
                $this->port = rand(3000, 9999);
                $this->outDir = 'assets/zest';
                $this->assetsDir = '.';
                $this->publicDir = 'assets';
                $this->urlPrefix = '/theme/' . $this->file->getParent()?->getName() . '/';
                $this->entry = 'src/main.js';
                $this->manifestName = 'manifest.json';
                break;

            default:
                $this->host = 'localhost';
                $this->port = rand(3000, 9999);
                $this->outDir = 'dist';
                $this->assetsDir = 'assets';
                $this->publicDir = 'public';
                $this->urlPrefix = null;
                $this->entry = 'src/main.js';
                $this->manifestName = 'manifest.json';
                break;
        }
    }



    /**
     * Get host
     */
    public function getHost(): ?string
    {
        return $this->host;
    }


    /**
     * Get port
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Should use HTTPS
     */
    public function shouldUseHttps(): bool
    {
        return $this->https ?? false;
    }

    /**
     * Get build dir
     */
    public function getOutDir(): ?string
    {
        return $this->outDir;
    }

    /**
     * Get assets dir
     */
    public function getAssetsDir(): ?string
    {
        return $this->assetsDir;
    }

    /**
     * Get public dir
     */
    public function getPublicDir(): ?string
    {
        return $this->publicDir;
    }

    /**
     * Get url prefix
     */
    public function getUrlPrefix(): ?string
    {
        return $this->urlPrefix;
    }

    /**
     * Get main entry file
     */
    public function getEntry(): ?string
    {
        return $this->entry;
    }

    /**
     * Get manifest name
     */
    public function getManifestName(): string
    {
        return $this->manifestName;
    }
}
