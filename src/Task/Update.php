<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Overpass;

class Update implements Task
{
    public function execute(): bool
    {
        Overpass::runNpm('update');

        return true;
    }
}
