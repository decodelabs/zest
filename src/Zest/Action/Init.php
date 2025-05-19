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
        protected Session $io
    ) {
    }

    public function execute(
        Request $request
    ): bool {
        if (!Zest::run('generate-package-config', '--no-install')) {
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
