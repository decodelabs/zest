<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Terminus as Cli;
use DecodeLabs\Zest;

class Init implements Task
{
    public function execute(): bool
    {
        /** @var array<string> */
        $plugins = Cli::prepareArguments();

        if (!Zest::run('generate-zest-config', '--check', ...$plugins)) {
            return false;
        }

        if (!Zest::run('generate-package-config', '--no-install', )) {
            return false;
        }

        if (!Zest::run('install-dependencies')) {
            return false;
        }

        if (!Zest::run('generate-vite-config')) {
            return false;
        }

        return Zest::run('dev');
    }
}
