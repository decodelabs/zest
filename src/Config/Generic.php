<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Config;

use DecodeLabs\Atlas\File;
use DecodeLabs\Coercion;
use DecodeLabs\Collections\Tree;
use DecodeLabs\Collections\Tree\NativeMutable as NativeTree;
use DecodeLabs\Zest\Config;
use DecodeLabs\Zest\Controller;

/**
 * @phpstan-type TConfig Tree<string|int|float|null>
 */
class Generic implements Config
{
    /**
     * @phpstan-var TConfig
     */
    protected Tree $data;

    protected File $file;
    protected Controller $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        $this->file = $controller->package->rootDir->getFile('Zest.php');
        $this->reload();
    }

    /**
     * Reload local data
     */
    public function reload(): void
    {
        if (!$this->file->exists()) {
            /** @phpstan-ignore-next-line */
            $this->data = new NativeTree();
            return;
        }

        $data = require (string)$this->file;
        /** @phpstan-ignore-next-line */
        $this->data = new NativeTree(Coercion::toArray($data));
    }


    /**
     * Get host
     */
    public function getHost(): ?string
    {
        return $this->data->host->as('?string');
    }


    /**
     * Get port
     */
    public function getPort(): ?int
    {
        return $this->data->port->as('?int');
    }

    /**
     * Should use HTTPS
     */
    public function shouldUseHttps(): bool
    {
        return $this->data->https->as('bool');
    }

    /**
     * Get plugins list
     */
    public function getPlugins(): array
    {
        /** @phpstan-ignore-next-line */
        return $this->data->plugins->getKeys();
    }

    /**
     * Get plugins config
     */
    public function getPluginConfig(): array
    {
        /** @phpstan-ignore-next-line */
        return $this->data->plugins->toArray();
    }

    /**
     * Get build dir
     */
    public function getOutDir(): ?string
    {
        return $this->data->outDir->as('?string');
    }

    /**
     * Get assets dir
     */
    public function getAssetsDir(): ?string
    {
        return $this->data->assetsDir->as('?string');
    }

    /**
     * Get url prefix
     */
    public function getUrlPrefix(): ?string
    {
        return $this->data->urlPrefix->as('?string');
    }

    /**
     * Get main entry file
     */
    public function getEntry(): ?string
    {
        return $this->data->entry->as('?string');
    }

    /**
     * Should output files contain hash
     */
    public function shouldHash(): bool
    {
        return $this->data->hash->as('bool');
    }

    /**
     * Get custom vite config overrides
     */
    public function getViteConfig(): Tree
    {
        return $this->data->vite;
    }
}
