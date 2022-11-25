<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Terminus;
use DecodeLabs\Zest;

class Build implements Task
{
    public function execute(): bool
    {
        Zest::checkPackage();

        Terminus::info('Building assets');
        Terminus::newLine();

        return Zest::$package->runScript('build');
    }
}
