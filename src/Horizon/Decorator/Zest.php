<?php

/**
 * @package Horizon
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Horizon\Decorator;

use DecodeLabs\Coercion;
use DecodeLabs\Horizon\Decorator;
use DecodeLabs\Horizon\Page;
use DecodeLabs\Iota;
use DecodeLabs\Monarch;
use DecodeLabs\Zest\Config;
use DecodeLabs\Zest\Manifest;

class Zest implements Decorator
{
    public function __construct(
        protected Iota $iota
    ) {
    }

    public function decorate(
        Page $page,
        string|Manifest|null $manifest = null
    ): void {
        $manifest = $this->loadManifest($manifest);

        foreach ($manifest->getCssData() as $file => $attributes) {
            /** @var array<string,string|bool|int|float> $attributes */
            $page->addLink(
                key: 'zest:' . $file,
                rel: 'stylesheet',
                href: $this->normalizeUrl($file),
                attributes: $attributes
            );
        }

        foreach ($manifest->getHeadJsData() as $file => $attributes) {
            /** @var array<string,string|bool|int|float> $attributes */
            $page->addScript(
                key: 'zest:' . $file,
                src: $this->normalizeUrl($file),
                attributes: $attributes
            );
        }

        foreach ($manifest->getBodyJsData() as $file => $attributes) {
            /** @var array<string,string|bool|int|float> $attributes */
            $page->addBodyScript(
                key: 'zest:' . $file,
                src: $this->normalizeUrl($file),
                attributes: $attributes
            );
        }
    }

    private function loadManifest(
        string|Manifest|null $manifest = null
    ): Manifest {
        if ($manifest instanceof Manifest) {
            return $manifest;
        }

        if (
            $manifest === null ||
            !str_contains($manifest, '/')
        ) {
            $manifest = $this->findManifest($manifest);
        } else {
            $manifest = Monarch::getPaths()->resolve($manifest);
        }

        return Manifest::load($manifest);
    }

    private function findManifest(
        ?string $manifest
    ): string {
        $repo = $this->iota->loadStatic('zest');

        if (!$repo->has('__manifest')) {
            return $this->getDefaultManifestPath();
        }


        /** @var array<string> */
        $list = Coercion::asArray($repo->return('__manifest'));

        if ($manifest === null) {
            $test = 'vite.config.php';
        } else {
            $test = 'vite.' . $manifest . '.config.php';
        }

        if (!in_array($test, $list)) {
            return $this->getDefaultManifestPath();
        }

        $config = $repo->returnAsType(
            key: $test,
            type: Config::class
        );

        return $config->path . '/' . $config->outDir . '/.vite/manifest.json';
    }

    private function getDefaultManifestPath(): string
    {
        return Monarch::getPaths()->run . '/public/assets/zest/.vite/manifest.json';
    }

    private function normalizeUrl(
        string $url
    ): string {
        if (
            str_starts_with($url, 'http://') ||
            str_starts_with($url, 'https://') ||
            str_starts_with($url, '//')
        ) {
            return $url;
        }

        if (!str_starts_with($url, '/')) {
            $url = '/' . $url;
        }

        return $url;
    }
}
