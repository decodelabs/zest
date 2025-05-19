<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Overpass\Project;

class Update implements Action
{
    public function execute(
        Request $request
    ): bool {
        return new Project()->update();
    }
}
