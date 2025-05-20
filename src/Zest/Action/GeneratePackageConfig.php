<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Atlas\File;
use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Argument;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Clip\Action\GenerateFileTrait;
use DecodeLabs\Overpass\Project;
use DecodeLabs\Terminus\Session;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Action\GeneratePackageConfig\PackageTemplate;

#[Argument\Flag(
    name: 'no-install',
    description: 'Don\'t install dependencies',
)]
class GeneratePackageConfig implements Action
{
    use GenerateFileTrait;

    protected function getTargetFile(): File
    {
        return new Project()->packageFile;
    }

    protected function getTemplate(): PackageTemplate
    {
        return new PackageTemplate(
            Zest::getController()
        );
    }

    protected function afterFileSave(
        File $file
    ): bool {
        if (!$this->request->parameters->asBool('no-install')) {
            return Zest::run('install-dependencies');
        }

        return true;
    }
}
