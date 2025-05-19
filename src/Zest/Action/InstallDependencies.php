<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Overpass\Project;

class InstallDependencies implements Action
{
    /**
     * @var array<string,string>
     */
    protected const array DevPackages = [
        'vite' => '^6',
        '@decodelabs/vite-plugin-zest' => '^0.3',
    ];

    public function execute(
        Request $request
    ): bool {
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
