<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Config;

use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\Zest\Config;

class Generic implements Config
{
    public function __construct(
        protected(set) string $path,
        protected(set) ?string $host = null,
        protected(set) ?int $port = null,
        protected(set) ?bool $https = false,
        protected(set) string $outDir = 'dist',
        protected(set) string $assetsDir = 'assets',
        protected(set) string $publicDir = 'public',
        /** @var array<string,string> */
        protected(set) array $aliases = [],
        protected(set) ?string $urlPrefix = null,
        protected(set) ?string $entry = null,
        protected(set) string $manifestName = 'manifest.json',
    ) {
        if (class_exists(Genesis::class)) {
            $path = Genesis::$hub->applicationPath;
        } else {
            $path = getcwd();
        }

        foreach ($aliases as $alias => $aliasPath) {
            if (str_starts_with($aliasPath, '.')) {
                $aliasPath = realpath($path . '/' . $aliasPath);

                if ($aliasPath === false) {
                    throw Exceptional::Runtime(
                        message: 'Could not resolve alias path: ' . $aliasPath
                    );
                }

                $aliases[$alias] = $aliasPath;
            }
        }

        $this->aliases = $aliases;

        if(!str_starts_with((string)$this->path, '/')) {
            if(false === ($newPath = realpath($path . '/' . $this->path))) {
                $newPath = $path . '/' . $this->path;
            }

            $this->path = $newPath;
        }
    }
}
