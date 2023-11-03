<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

interface Config
{
    public function reload(): void;

    public function loadDefaults(
        ?string $name = null
    ): void;


    public function getHost(): ?string;
    public function getPort(): ?int;

    public function shouldUseHttps(): bool;

    public function getOutDir(): ?string;
    public function getAssetsDir(): ?string;
    public function getPublicDir(): ?string;
    public function getUrlPrefix(): ?string;

    public function getEntry(): ?string;
    public function getManifestName(): string;
}
