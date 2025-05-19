<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

declare(ticks=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Zest;

class Dev implements Action
{
    use ViteTrait;

    public function execute(
        Request $request
    ): bool {
        Zest::checkProject();

        $configName = $this->getConfigFileName($request);

        return Zest::$project->runPackage(
            'vite',
            $this->getConfigArgument($configName)
        );
    }
}
