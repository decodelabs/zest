<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Plugin;

use DecodeLabs\Zest\Plugin;

class Stylus implements Plugin
{
    public function getName(): string
    {
        return 'stylus';
    }

    /**
     * Get production dependencies
     */
    public function getPackages(): array
    {
        return [];
    }

    /**
     * Get dev dependencies
     */
    public function getDevPackages(): array
    {
        return [
            'stylus' => null
        ];
    }

    /**
     * Get config imports list
     */
    public function getImports(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): ?array
    {
        return null;
    }
}
