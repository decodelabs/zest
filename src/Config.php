<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

use DecodeLabs\Collections\Tree;

interface Config
{
    public function reload(): void;

    public function getHost(): ?string;
    public function getPort(): ?int;

    public function shouldUseHttps(): bool;

    /**
     * @return array<string>
     */
    public function getPlugins(): array;

    /**
     * @return array<string, mixed>
     */
    public function getPluginConfig(): array;

    public function getOutDir(): ?string;
    public function getAssetsDir(): ?string;
    public function getPublicDir(): ?string;
    public function getUrlPrefix(): ?string;

    public function getEntry(): ?string;
    public function shouldHash(): bool;

    /**
     * @phpstan-return Tree<string|int|float|null>
     */
    public function getViteConfig(): Tree;
}
