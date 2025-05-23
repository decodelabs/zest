<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\File;
use DecodeLabs\Collections\Tree;

class Manifest
{
    protected File $file;
    protected File $genFile;


    /**
     * @var array<string,array<string,mixed>>
     */
    protected array $headJs = [];

    /**
     * @var array<string,array<string,mixed>>
     */
    protected array $bodyJs = [];

    /**
     * @var array<string,array<string,mixed>>
     */
    protected array $css = [];

    protected bool $hot = false;


    /**
     * Load manifest in production mode
     */
    public static function load(
        string|File $file
    ): static {
        if (!$file instanceof File) {
            $file = Atlas::file($file);
        }

        $genFile = Atlas::file((string)$file . '.php');

        if (!$genFile->exists()) {
            return new static($file);
        }

        /** @var static $output */
        $output = require $genFile;
        return $output;
    }


    final public function __construct(
        string|File $file,
        bool $hot = false
    ) {
        if (!$file instanceof File) {
            $file = Atlas::file($file);
        }

        $this->file = $file;
        $this->genFile = Atlas::file((string)$file . '.php');
        $this->hot = $hot;
    }



    /**
     * Add head JS files
     *
     * @param array<string, array<string, mixed>> $files
     * @return $this
     */
    public function addHeadJs(
        array $files
    ): static {
        foreach ($files as $file => $attrs) {
            $this->headJs[$file] = $attrs;
        }

        return $this;
    }

    /**
     * Get head JS attributes
     *
     * @return array<string, array<string, mixed>>
     */
    public function getHeadJsData(): array
    {
        return $this->headJs;
    }

    /**
     * Add body JS files
     *
     * @param array<string, array<string, mixed>> $files
     * @return $this
     */
    public function addBodyJs(
        array $files
    ): static {
        foreach ($files as $file => $attrs) {
            $this->bodyJs[$file] = $attrs;
        }

        return $this;
    }

    /**
     * Get body JS attributes
     *
     * @return array<string, array<string, mixed>>
     */
    public function getBodyJsData(): array
    {
        return $this->bodyJs;
    }

    /**
     * Add CSS files
     *
     * @param array<string, array<string, mixed>> $files
     * @return $this
     */
    public function addCss(
        array $files
    ): static {
        foreach ($files as $file => $attrs) {
            $this->css[$file] = $attrs;
        }

        return $this;
    }

    /**
     * Get CSS attributes
     *
     * @return array<string, array<string, mixed>>
     */
    public function getCssData(): array
    {
        return $this->css;
    }

    /**
     * Is manifest for dev mode?
     */
    public function isHot(): bool
    {
        return $this->hot;
    }


    /**
     * Load json data
     *
     * @return Tree<string|bool|float|int>
     */
    public function loadData(): Tree
    {
        if (!$this->file->exists()) {
            $data = [];
        } else {
            /** @var array<mixed> */
            $data = json_decode($this->file->getContents(), true);
        }

        /**
         * @var Tree<string|bool|float|int> $output
         * @phpstan-ignore-next-line
         */
        $output = new Tree($data);
        return $output;
    }

    /**
     * Save cache file
     *
     * @return $this
     */
    public function save(): static
    {
        if (
            empty($this->headJs) &&
            empty($this->bodyJs) &&
            empty($this->css)
        ) {
            return $this;
        }

        $output =
            '<?php' . "\n\n" .
            'namespace DecodeLabs\\Zest\\Cache;' . "\n\n" .
            'use DecodeLabs\\Zest\\Manifest;' . "\n\n" .
            '/* Auto-generated Zest manifest cache file */' . "\n" .
            'return (new Manifest(__DIR__ . \'/manifest.json\', ' . ($this->hot ? 'true' : 'false') . '))' . "\n";

        if (!empty($this->headJs)) {
            $output .= '    ->addHeadJs(' . $this->exportArray($this->headJs) . ')' . "\n";
        }

        if (!empty($this->bodyJs)) {
            $output .= '    ->addBodyJs(' . $this->exportArray($this->bodyJs) . ')' . "\n";
        }

        if (!empty($this->css)) {
            $output .= '    ->addCss(' . $this->exportArray($this->css) . ')' . "\n";
        }

        $output .= ';' . "\n";

        $this->genFile->putContents($output);
        return $this;
    }

    /**
     * Convert array to string neatly
     *
     * @param array<mixed> $array
     */
    protected function exportArray(
        array $array,
        int $level = 1
    ): string {
        $output = '[';

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->exportArray($value, $level + 1);
            } else {
                $value = var_export($value, true);
            }

            $output .= "\n    " . var_export($key, true) . ' => ' . $value . ',';
        }

        if (count($array) > 0) {
            $output .= "\n";
        }

        $output .= ']';
        return str_replace("\n", "\n    ", $output);
    }


    /**
     * Generate production manifest
     */
    public static function generateProduction(
        string|File $file,
        Config $config
    ): static {
        if (!$file instanceof File) {
            $file = Atlas::file($file);
        }

        $output = (new static($file));

        $output->headJs = [];
        $output->bodyJs = [];
        $output->css = [];

        if (!$output->file->exists()) {
            return $output;
        }

        $data = $output->loadData();
        $prefix = trim((string)$config->urlPrefix, '/');

        $publicDir = (string)$config->publicDir;
        $outDir = (string)$config->outDir;

        if (str_starts_with($outDir, $publicDir)) {
            $prefix .= '/' . trim(substr($outDir, strlen($publicDir)), '/');
        } elseif (str_starts_with($outDir, 'assets')) {
            $prefix .= '/' . trim(substr($outDir, 6), '/');
        } elseif (str_starts_with($outDir, '../assets')) {
            $prefix .= '/' . trim(substr($outDir, 9), '/');
        }

        $styles = [];

        foreach ($data as $file) {
            // JS
            if (
                $file['isEntry'] &&
                str_ends_with((string)$file['file'], '.js')
            ) {
                $filePath = ltrim($prefix . '/' . $file['file'], '.');
                $output->bodyJs[$filePath] = static::getJsFileAttrs((string)$file['file']);
            }

            // CSS
            if (isset($file->css)) {
                foreach ($file->css as $cssFile) {
                    $filePath = ltrim($prefix . '/' . $cssFile->getValue(), '.');
                    $output->css[$filePath] = [];
                }
            } elseif (
                str_ends_with((string)$file['src'], '.css') ||
                str_ends_with((string)$file['file'], '.css')
            ) {
                $filePath = ltrim($prefix . '/' . $file['file'], '.');
                $styles[$filePath] = [];
            }
        }

        // CSS code splitting turned off
        if (
            empty($output->css) &&
            !empty($styles)
        ) {
            $output->css = $styles;
        }

        $output->save();
        return $output;
    }

    /**
     * Generate dev manifest
     */
    public static function generateDev(
        string|File $file,
        Config $config
    ): static {
        if (!$file instanceof File) {
            $file = Atlas::file($file);
        }

        $url = $config->https ? 'https' : 'http';
        $url .= '://' . $config->host . ':' . $config->port;
        $url .= '/' . trim((string)$config->urlPrefix, '/');
        $url = rtrim($url, '/');

        $output = new static($file, true);
        $entry = $config->entry ?? 'src/main.js';

        $output->addHeadJs([
            $url . '/@vite/client' => static::getJsFileAttrs('@vite/client')
        ]);

        $output->addBodyJs([
            $url . '/' . $entry => static::getJsFileAttrs($entry)
        ]);

        $output->save();
        return $output;
    }

    /**
     * @return array<string, mixed>
     */
    protected static function getJsFileAttrs(
        string $file
    ): array {
        $output = [];

        if (false !== strpos($file, '-legacy')) {
            $output['nomodule'] = true;
        } else {
            $output['type'] = 'module';
        }

        return $output;
    }
}
