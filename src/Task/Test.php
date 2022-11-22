<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Hub;
use DecodeLabs\Clip\Task;
use DecodeLabs\Genesis;

class Test implements Task
{
    public function execute(): void
    {
        dd(
            Genesis::$hub
                ->as(Hub::class)
                ->composerFile
        );
    }
}
