<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

use DecodeLabs\Atlas\File;
use DecodeLabs\Coercion;
use DecodeLabs\Genesis\FileTemplate;

class Template extends FileTemplate
{
    public const FILE = 'file.template';

    protected Controller $controller;

    public function __construct(
        Controller $controller,
        string|File|null $file = null
    ) {
        $this->controller = $controller;
        parent::__construct($file ?? static::FILE);
    }

    protected function generateSlot(string $name): ?string
    {
        switch ($name) {
            case 'host':
                return $this->controller->config->getHost() ?? 'localhost';

            case 'port':
                return Coercion::toStringOrNull($this->controller->config->getPort()) ??
                    $this->getDefaultPort();

            case 'outDir':
                return $this->controller->config->getOutDir() ?? 'dist';

            case 'assetsDir':
                return $this->controller->config->getAssetsDir() ?? 'assets';

            case 'entry':
                return $this->controller->config->getEntry() ?? 'src/main.js';
        }

        return parent::generateSlot($name);
    }

    /**
     * Create default port prompt
     */
    protected function getDefaultPort(): string
    {
        return (string)rand(3000, 9999);
    }

    /**
     * @param array<mixed> $data
     */
    protected function exportArray(
        array $data,
        int $indent = 0
    ): string {
        $output = '[';
        $indent++;

        if ($isAssoc = $this->isAssoc($data)) {
            $output .= "\n";
        }

        foreach ($data as $key => $value) {
            if ($isAssoc) {
                $output .= str_repeat('    ', $indent);
                $output .= var_export($key, true) . ' => ';
            }

            if (is_array($value)) {
                $output .= $this->exportArray($value, $indent);
            } elseif (is_bool($value)) {
                $output .= $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $output .= 'null';
            } else {
                $output .= var_export($value, true);
            }

            $output .= ',';

            if ($isAssoc) {
                $output .= "\n";
            }
        }

        if ($isAssoc) {
            $output .= str_repeat('    ', $indent - 1);
        } else {
            $output = rtrim($output, ',');
        }

        $output .= ']';
        return $output;
    }

    /**
     * @param array<mixed> $arr
     */
    protected function isAssoc(array $arr): bool
    {
        if ($arr === []) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }


    /**
     * Export json data
     */
    protected function exportJson(
        mixed $data,
        int $indent = 0
    ): string {
        $output = (string)str_replace(
            "\n",
            "\n" . str_repeat('    ', $indent),
            (string)json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $output = (string)preg_replace('/"([^"]+)"\:/', '$1:', $output);
        $output = (string)str_replace('"', "'", $output);
        $output = (string)str_replace(["'`", "`'"], '`', $output);

        return $output;
    }
}
