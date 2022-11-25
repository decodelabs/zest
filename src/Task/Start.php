<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
declare(ticks=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Terminus;
use DecodeLabs\Zest;

class Start implements Task
{
    public function execute(): bool
    {
        Zest::checkPackage();

        Zest::$package->runServerScript('dev');
        Terminus::newLine();

        return Zest::run('build');
    }
}
