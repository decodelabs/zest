<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task\GenerateViteConfig;

use DecodeLabs\Coercion;
use DecodeLabs\Collections\Tree\NativeMutable as NativeTree;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Template;

class ViteTemplate extends Template
{
    public const FILE = __DIR__ . '/vite.template';

    protected function generateSlot(string $name): ?string
    {
        switch ($name) {
            case 'config':
                return $this->exportConfig();

            case 'root':
                return (string)Zest::$package->rootDir;

            case 'pluginImports':
                return $this->getPluginImports();

            case 'plugins':
                return $this->getPluginConfig();
        }

        return parent::generateSlot($name);
    }

    /**
     * Export config
     */
    protected function exportConfig(): string
    {
        $data = new NativeTree([
            'plugins' => '{{ plugins }}',
            'root' => $this->getSlot('root'),
            'build' => [
                'outDir' => $this->getSlot('outDir'),
                'assetsDir' => $this->getSlot('assetsDir'),
                'manifest' => true,
                'rollupOptions' => $this->getRollupOptions()
            ],
            'server' => [
                'host' => $this->getSlot('host'),
                'port' => Coercion::toInt($this->getSlot('port')),
                'https' => $this->controller->config->shouldUseHttps(),
                'strictPort' => true,
                'hmr' => [
                    'protocol' => 'ws',
                    'host' => $this->getSlot('host')
                ]
            ]
        ]);

        $data->merge($this->controller->config->getViteConfig());
        $data = $data->toArray();
        $output = $this->exportJson($data);

        return str_replace(
            "'{{ plugins }}'",
            $this->getPluginConfig(),
            $output
        );
    }

    /**
     * Get plugin imports list
     */
    protected function getPluginImports(): string
    {
        $output = [];
        $index = [];

        foreach ($this->controller->config->getPlugins() as $name) {
            $plugin = Zest::getPlugin($name);
            $imports = $plugin->getImports();

            foreach ($imports as $package => $targets) {
                if (!isset($index[$package])) {
                    $index[$package] = [];
                }

                $index[$package] = array_unique(array_merge(
                    $index[$package],
                    $targets
                ));
            }
        }

        foreach ($index as $package => $targets) {
            $str = 'import ';

            if (count($targets) === 1) {
                $str .= current($targets) . ' ';
            } else {
                $str .=
                    '{' . "\n" .
                    '    ' . implode(",\n", $targets) . "\n" .
                    '} ';
            }

            $str .= 'from \'' . $package . '\'';
            $output[] = $str;
        }

        return implode("\n", $output);
    }

    /**
     * Get plugin config
     */
    protected function getPluginConfig(): string
    {
        $output = [];

        foreach ($this->controller->config->getPluginConfig() as $name => $config) {
            $plugin = Zest::getPlugin($name);
            $imports = $plugin->getImports();

            if (empty($imports)) {
                continue;
            }

            $str = $plugin->getName() . '(';

            if ($config !== null) {
                $str .= str_replace(
                    "\n",
                    "\n        ",
                    (string)json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                );
            }

            $str .= ')';
            $output[] = $str;
        }

        return '[' . "\n" .
        '        ' . implode(",\n        ", $output) . "\n" .
        '    ]';
    }

    /**
     * Get rollup options
     *
     * @return array<string, mixed>
     */
    protected function getRollupOptions(): array
    {
        $data = [
            'input' => $this->controller->config->getEntry() ?? 'src/main.js'
        ];

        if (!$this->controller->config->shouldHash()) {
            $dir = $this->controller->config->getAssetsDir();

            if ($dir === '.') {
                $dir = null;
            } elseif (!empty($dir)) {
                $dir .= '/';
            }

            $data['output'] = [
                'entryFileNames' => '`' . $dir . '[name].js`',
                'chunkFileNames' => '`' . $dir . '[name].js`',
                'assetFileNames' => '`' . $dir . '[name].[ext]`'
            ];
        }

        return $data;
    }
}
