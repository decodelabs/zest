<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action\GenerateViteConfig;

use DecodeLabs\Terminus\Session;
use DecodeLabs\Zest\Config;
use DecodeLabs\Zest\Template;

class ViteTemplate extends Template
{
    protected const string File = __DIR__ . '/vite.template';

    public function __construct(
        protected Config $config,
        protected Session $io
    ) {
        parent::__construct();
    }

    protected function generateSlot(
        string $name
    ): ?string {
        switch ($name) {
            case 'port':
                return $this->io->ask('What port should vite run on?', (string)($this->config->port ?? rand(3000, 9999)));

            case 'entry':
                return $this->io->ask('What is your main entry file?', $this->config->entry ?? 'src/main.js');
        }

        return parent::generateSlot($name);
    }
}
