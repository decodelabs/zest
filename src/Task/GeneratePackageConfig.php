<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Atlas\File;
use DecodeLabs\Clip\Task;
use DecodeLabs\Clip\Task\GenerateFileTrait;
use DecodeLabs\Overpass;
use DecodeLabs\Terminus as Cli;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Task\GeneratePackageConfig\PackageTemplate;

class GeneratePackageConfig implements Task
{
    use GenerateFileTrait;

    protected function getTargetFile(): File
    {
        return Overpass::$rootDir->getFile('package.json');
    }

    protected function getTemplate(): PackageTemplate
    {
        return new PackageTemplate(
            Zest::getController()
        );
    }

    protected function afterFileSave(File $file): bool
    {
        Cli::getCommandDefinition()
            ->addArgument('-no-install', 'Don\'t install dependencies');
        Cli::prepareArguments();


        if (!Cli::getArgument('no-install')) {
            return Zest::run('install-dependencies');
        }

        return true;
    }
}
