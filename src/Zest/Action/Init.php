<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Terminus\Session;
use DecodeLabs\Zest;

class Init implements Action
{
    public function __construct(
        protected Session $io,
        protected Zest $zest
    ) {
    }

    public function execute(
        Request $request
    ): bool {
        if (!$this->zest->run('generate-package-config', '--no-install')) {
            return false;
        }

        if (!$this->zest->run('install-dependencies')) {
            return false;
        }

        if (!$this->zest->run('generate-vite-config')) {
            return false;
        }

        return $this->zest->run('dev');
    }
}
