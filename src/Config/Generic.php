<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Config;

use DecodeLabs\Atlas\File;
use DecodeLabs\Coercion;
use DecodeLabs\Exceptional;
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
    protected File $jsonFile;
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
        $this->jsonFile = $controller->package->rootDir->getFile($name . 'on');
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

        $conf = $this->file->getContents();
        $jsonConf = $this->jsonFile->exists() ? $this->jsonFile->getContents() : null;

        $this->host = Coercion::toString($this->extractValue($conf, $jsonConf, 'host', true) ?? 'localhost');
        $this->port = Coercion::toInt($this->extractValue($conf, $jsonConf, 'port'));
        $this->https = Coercion::toBool($this->extractValue($conf, $jsonConf, 'https', true));
        $this->outDir = Coercion::toString($this->extractValue($conf, $jsonConf, 'outDir'));
        $this->assetsDir = Coercion::toString($this->extractValue($conf, $jsonConf, 'assetsDir'));
        $this->publicDir = Coercion::toString($this->extractValue($conf, $jsonConf, 'publicDir'));
        $this->urlPrefix = Coercion::toStringOrNull($this->extractValue($conf, $jsonConf, 'base', true));
        $this->entry = Coercion::toStringOrNull($this->extractValue($conf, $jsonConf, 'input', true));

        $manifest = $this->extractValue($conf, $jsonConf, 'manifest', true);

        if (is_string($manifest)) {
            $this->manifestName = $manifest;
        } else {
            $this->manifestName = 'manifest.json';
        }
    }


    protected function extractValue(
        string $conf,
        ?string $jsonConf,
        string $key,
        bool $nullable = false
    ): string|int|bool|null {
        $key = preg_quote($key, '/');
        $matches = [];

        if (preg_match('/' . $key . ':\s*(?<value>.+?),/', $conf, $matches)) {
            return $this->normalizeConfigValue($matches['value']);
        }

        if (
            $jsonConf &&
            preg_match('/"' . $key . '":\s*(?<value>.+?),/', $jsonConf, $matches)
        ) {
            return $this->normalizeConfigValue($matches['value']);
        }

        if ($nullable) {
            return null;
        }

        throw Exceptional::UnexpectedValue(
            'Unable to extract ' . $key . ' from vite.config.js'
        );
    }

    protected function normalizeConfigValue(
        string $value
    ): string|int|bool|null {
        $value = trim($value);

        if (
            substr($value, 0, 1) === "'" ||
            substr($value, 0, 1) === '"'
        ) {
            $value = substr($value, 1, -1);
        }

        if ($value === 'true') {
            return true;
        } elseif ($value === 'false') {
            return false;
        } elseif ($value === 'null') {
            return null;
        }

        return $value;
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
