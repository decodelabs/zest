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
    protected const string File = __DIR__ . '/vite.template';

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
            case 'port':
                return Cli::ask('What port should vite run on?', (string)($this->config->port ?? rand(3000, 9999)));

            case 'entry':
                return Cli::ask('What is your main entry file?', $this->config->entry ?? 'src/main.js');
        }

        return parent::generateSlot($name);
    }
}
