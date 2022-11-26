<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

interface Plugin
{
    public function getName(): string;

    /**
     * @return array<string, ?string>
     */
    public function getPackages(): array;

    /**
     * @return array<string, ?string>
     */
    public function getDevPackages(): array;

    /**
     * @return array<string, array<string>>
     */
    public function getImports(): array;

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): ?array;
}
