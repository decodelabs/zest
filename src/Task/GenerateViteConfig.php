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
use DecodeLabs\Zest;
use DecodeLabs\Zest\Task\GenerateViteConfig\ViteTemplate;

class GenerateViteConfig implements Task
{
    use GenerateFileTrait;

    protected function getTargetFile(): File
    {
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
        return true;
    }
}
