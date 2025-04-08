<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Atlas\File;
use DecodeLabs\Clip\Task;
use DecodeLabs\Clip\Task\BeforeHook;
use DecodeLabs\Clip\Task\GenerateFileTrait;
use DecodeLabs\Coercion;
use DecodeLabs\Overpass;
use DecodeLabs\Terminus as Cli;
use DecodeLabs\Zest;
use DecodeLabs\Zest\Config\Vite as Config;
use DecodeLabs\Zest\Task\GenerateViteConfig\ViteTemplate;
use DecodeLabs\Zest\Template;

class GenerateViteConfig implements Task, BeforeHook
{
    use GenerateFileTrait;

    protected Config $config;

    public function beforeExecute(): bool
    {
        Cli::$command
            ->addArgument('defaults=default', 'Defaults set name');

        $this->config = Zest::loadConfig();
        $this->config->loadDefaults();

        return true;
    }

    protected function getTargetFile(): File
    {
        return Overpass::$rootDir->getFile('vite.config.js');
    }

    protected function getTemplate(): ViteTemplate
    {
        return new ViteTemplate(
            Zest::getController(),
            $this->config
        );
    }

    protected function afterFileSave(
        File $file
    ): bool {
        $this->config->reload();


        // Ensure main.js exists
        if (null !== ($entry = $this->config->entry)) {
            $file = Zest::$package->rootDir->getFile($entry);

            if (!$file->exists()) {
                $file->putContents('console.log("hello world")');
            }
        }

        return true;
    }
}
