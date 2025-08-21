<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Atlas\File;
use DecodeLabs\Clip\Action\GenerateFileTrait;
use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Argument;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Terminus\Session;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Action\GeneratePackageConfig\PackageTemplate;

#[Argument\Flag(
    name: 'no-install',
    description: 'Don\'t install dependencies',
)]
class GeneratePackageConfig implements Action
{
    use GenerateFileTrait {
        __construct as private __generateFileTraitConstruct;
    }

    public function __construct(
        protected Session $io,
        protected Request $request,
        protected Zest $zest
    ) {
        $this->__generateFileTraitConstruct($io, $request);
    }

    protected function getTargetFile(): File
    {
        return $this->zest->project->packageFile;
    }

    protected function getTemplate(): PackageTemplate
    {
        return new PackageTemplate(
            $this->zest,
            $this->io
        );
    }

    protected function afterFileSave(
        File $file
    ): bool {
        if (!$this->request->parameters->asBool('no-install')) {
            return $this->zest->run('install-dependencies');
        }

        return true;
    }
}
