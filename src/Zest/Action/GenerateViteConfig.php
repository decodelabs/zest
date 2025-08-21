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
use DecodeLabs\Zest\Action\GenerateViteConfig\ViteTemplate;
use DecodeLabs\Zest\Config\Vite as Config;

#[Argument\Value(
    name: 'confName',
    description: 'Config name',
)]
class GenerateViteConfig implements Action
{
    use GenerateFileTrait {
        __construct as private __generateFileTraitConstruct;
    }

    protected Config $config {
        get {
            if (isset($this->config)) {
                return $this->config;
            }

            $this->config = $this->zest->loadConfig();
            $this->config->loadDefaults();
            return $this->config;
        }
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
        $fileName = 'vite.';

        if ($confName = $this->request->parameters->tryString('confName')) {
            $fileName .= $confName . '.';
        }

        $fileName .= 'config.ts';
        return $this->zest->project->rootDir->getFile($fileName);
    }

    protected function getTemplate(): ViteTemplate
    {
        return new ViteTemplate(
            $this->config,
            $this->io
        );
    }

    protected function afterFileSave(
        File $file
    ): bool {
        $this->config->reload();


        // Ensure main.js exists
        if (null !== ($entry = $this->config->entry)) {
            $file = $this->zest->project->rootDir->getFile($entry);

            if (!$file->exists()) {
                $file->putContents('console.log("hello world")');
            }
        }

        return true;
    }
}
