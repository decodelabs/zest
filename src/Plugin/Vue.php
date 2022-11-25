<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Plugin;

use DecodeLabs\Zest\Plugin;

class Vue implements Plugin
{
    public function getName(): string
    {
        return 'vue';
    }

    /**
     * Get production dependencies
     */
    public function getPackages(): array
    {
        return [
            'vue' => '^3.2'
        ];
    }

    /**
     * Get dev dependencies
     */
    public function getDevPackages(): array
    {
        return [
            '@vitejs/plugin-vue' => '^3.2'
        ];
    }

    /**
     * Get config imports list
     */
    public function getImports(): array
    {
        return [
            '@vitejs/plugin-vue' => ['vue']
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): ?array
    {
        return null;
    }
}
