<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Task;

use DecodeLabs\Clip\Task;
use DecodeLabs\Overpass\Project;

class InstallDependencies implements Task
{
    /**
     * @var array<string,string>
     */
    protected const array DevPackages = [
        'vite' => '^6',
        '@decodelabs/vite-plugin-zest' => '^0.1.1',
    ];

    public function execute(): bool
    {
        $project = new Project();
        $packages = $devPackages = [];

        foreach (static::DevPackages as $name => $version) {
            $devPackages[$name] = $name.'@'.$version;
        }

        //$project->install(...array_values($packages));
        $project->installDev(...array_values($devPackages));

        return true;
    }
}
