<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build\Provider;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Genesis\Build\Provider;
use Generator;

class Zest implements Provider
{
    public string $name = 'zest';

    public function __construct()
    {
    }

    public function scanBuildItems(
        Dir $rootDir
    ): Generator {
        yield $rootDir->getDir('public') => 'public/';
    }
}
