<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Request;

class Update implements Action
{
    use ViteTrait;

    public function execute(
        Request $request
    ): bool {
        return $this->zest->project->update();
    }
}
