<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task\GenerateZestConfig;

use DecodeLabs\Terminus as Cli;
use DecodeLabs\Zest\Controller;
use DecodeLabs\Zest\Template;

class ZestTemplate extends Template
{
    public const FILE = __DIR__ . '/zest.template';

    /**
     * @var array<string>
     */
    protected array $plugins = [];

    /**
     * @param array<string> $plugins
     */
    public function __construct(
        Controller $controller,
        array $plugins
    ) {
        parent::__construct($controller);
        $this->plugins = $plugins;
    }

    protected function generateSlot(string $name): ?string
    {
        switch ($name) {
            case 'host':
                return Cli::ask('What host should vite use?', 'localhost');

            case 'outDir':
                return Cli::ask('Where should your builds go?', 'dist');

            case 'https':
                return Cli::confirm('Should vite use HTTPS?', true) ? 'true' : 'false';

            case 'assetsDir':
                return Cli::ask('Where should your assets go within builds?', 'assets');

            case 'entry':
                return Cli::ask('What is your main entry file?', 'src/main.js');

            case 'hash':
                return Cli::confirm('Should output files contain hashes?', true) ? 'true' : 'false';

            case 'plugins':
                return $this->getPluginList();
        }

        return parent::generateSlot($name);
    }

    protected function getDefaultPort(): string
    {
        return (string)Cli::ask('What port should vite run on?', parent::getDefaultPort());
    }

    protected function getPluginList(): string
    {
        $output = [];

        foreach ($this->plugins as $plugin) {
            $plugin = $this->controller->getPlugin($plugin);
            $output[$plugin->getName()] = $plugin->getConfig();
        }

        return $this->exportArray($output, 1);
    }
}
