<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Argument;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Zest;

#[Argument\Flag(
    name: 'emptyOutDir',
    description: 'Empty out dir',
)]
class Build implements Action
{
    use ViteTrait;

    public function execute(
        Request $request
    ): bool {
        Zest::checkProject();

        $this->io->info('Building assets');
        $this->io->newLine();

        $configName = $this->getConfigFileName($request);

        return Zest::$project->runPackage(
            'vite',
            'build',
            ...$this->getBuildArguments($request, $configName)
        );
    }
}
