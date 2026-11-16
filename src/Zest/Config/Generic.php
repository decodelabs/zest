<?php

/**
 * Zest
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Config;

use DecodeLabs\Exceptional;
use DecodeLabs\Monarch;
use DecodeLabs\Zest\Config;

class Generic implements Config
{
    public function __construct(
        public protected(set) string $path,
        public protected(set) ?string $host = null,
        public protected(set) ?int $port = null,
        public protected(set) ?bool $https = false,
        public protected(set) ?string $origin = null,
        public protected(set) string $outDir = 'dist',
        public protected(set) string $assetsDir = 'assets',
        public protected(set) string $publicDir = 'public',
        /** @var array<string,string> */
        public protected(set) array $aliases = [],
        public protected(set) ?string $urlPrefix = null,
        public protected(set) ?string $entry = null,
        public protected(set) string $manifestName = 'manifest.json',
    ) {
        $path = Monarch::getPaths()->run;

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

        if (!str_starts_with((string)$this->path, '/')) {
            if (false === ($newPath = realpath($path . '/' . $this->path))) {
                $newPath = $path . '/' . $this->path;
            }

            $this->path = $newPath;
        }
    }
}
