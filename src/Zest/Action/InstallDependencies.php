<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest\Action;

use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Request;

class InstallDependencies implements Action
{
    use ViteTrait;

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
        $packages = $devPackages = [];

        foreach (static::DevPackages as $name => $version) {
            $devPackages[$name] = $name . '@' . $version;
        }

        //$project->install(...array_values($packages));
        $this->zest->project->installDev(...array_values($devPackages));

        return true;
    }
}
