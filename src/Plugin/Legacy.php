<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Plugin;

use DecodeLabs\Zest\Plugin;

class Legacy implements Plugin
{
    public function getName(): string
    {
        return 'legacy';
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
            '@vitejs/plugin-legacy' => '^2.3',
            'terser' => null
        ];
    }

    /**
     * Get config imports list
     */
    public function getImports(): array
    {
        return [
            '@vitejs/plugin-legacy' => ['legacy']
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): ?array
    {
        return [
            'targets' => ['defaults', 'not IE 11']
        ];
    }
}
