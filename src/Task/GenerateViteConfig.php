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
use DecodeLabs\Zest\Task\GenerateViteConfig\ViteTemplate;
use DecodeLabs\Zest\Template;

class GenerateViteConfig implements Task
{
    use GenerateFileTrait;

    protected function getTargetFile(): File
    {
        Cli::getCommandDefinition()
            ->addArgument('defaults=default', 'Defaults set name');

        /** @var array<string, string> $args */
        $args = Cli::prepareArguments();
        Zest::$config->loadDefaults($args['defaults']);

        return Overpass::$rootDir->getFile('vite.config.js');
    }

    protected function getTemplate(): ViteTemplate
    {
        return new ViteTemplate(
            Zest::getController()
        );
    }

    protected function afterFileSave(File $file): bool
    {
        Zest::$config->reload();

        // Ensure index.html exists
        $index = Overpass::$rootDir->getFile('index.html');

        if (!$index->exists()) {
            (new Template(
                Zest::getController(),
                __DIR__ . '/GenerateViteConfig/index.template'
            ))
                ->saveTo($index);
        }


        // Ensure main.js exists
        if (null !== ($entry = Zest::$config->getEntry())) {
            $file = Zest::$package->rootDir->getFile($entry);

            if (!$file->exists()) {
                $file->putContents('console.log("hello world")');
            }
        }

        return true;
    }
}
