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
use DecodeLabs\Zest\Task\GenerateZestConfig\ZestTemplate;

class GenerateZestConfig implements Task
{
    use GenerateFileTrait;

    protected function getTargetFile(): File
    {
        return Overpass::$rootDir->getFile('Zest.php');
    }

    protected function getTemplate(): ZestTemplate
    {
        return new ZestTemplate(
            Zest::getController(),
            $this->getPluginNames()
        );
    }

    protected function afterFileSave(File $file): bool
    {
        Zest::$config->reload();

        if (null !== ($entry = Zest::$config->getEntry())) {
            $file = Zest::$package->rootDir->getFile($entry);

            if (!$file->exists()) {
                $file->putContents('console.log("hello world")');
            }
        }

        return true;
    }

    /**
     * @return array<string>
     */
    protected function getPluginNames(): array
    {
        $output = [];
        $plugins = Cli::prepareArguments();

        foreach ($plugins as $key => $name) {
            if (
                substr($key, 0, 7) !== 'unnamed' ||
                !is_string($name)
            ) {
                continue;
            }

            $output[] = $name;
        }

        if (empty($output)) {
            $output = explode(' ', (string)Cli::ask('What plugins do you want to use?', 'vue legacy'));
        }

        return $output;
    }
}
