<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task\GenerateViteConfig;

use DecodeLabs\Terminus as Cli;
use DecodeLabs\Zest\Config;
use DecodeLabs\Zest\Controller;
use DecodeLabs\Zest\Template;

class ViteTemplate extends Template
{
    public const FILE = __DIR__ . '/vite.template';

    protected Config $config;

    public function __construct(
        Controller $controller,
        Config $config
    ) {
        parent::__construct($controller);
        $this->config = $config;
    }

    protected function generateSlot(
        string $name
    ): ?string {
        switch ($name) {
            case 'host':
                return Cli::ask('What host should vite use?', $this->config->getHost() ?? 'localhost');

            case 'port':
                return Cli::ask('What port should vite run on?', (string)($this->config->getPort() ?? rand(3000, 9999)));

            case 'outDir':
                return Cli::ask('Where should your builds go?', $this->config->getOutDir() ?? 'dist');

            case 'assetsDir':
                return Cli::ask('Where should your assets go within builds?', $this->config->getAssetsDir() ?? 'assets');

            case 'publicDir':
                return Cli::ask('Where are your public assets located?', $this->config->getPublicDir() ?? 'public');

            case 'urlPrefix':
                $out = Cli::ask('What url prefix should production paths use?', $this->config->getUrlPrefix() ?? '/');
                $out = trim((string)$out, '/') . '/';

                if ($out !== '/') {
                    $out = '/' . $out;
                }

                return $out;

            case 'entry':
                return Cli::ask('What is your main entry file?', $this->config->getEntry() ?? 'src/main.js');
        }

        return parent::generateSlot($name);
    }
}
