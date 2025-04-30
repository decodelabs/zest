<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Clip\Controller as ControllerInterface;
use DecodeLabs\Clip\Controller\Generic as GenericController;
use DecodeLabs\Exceptional;
use DecodeLabs\Overpass\Project;
use DecodeLabs\Veneer\Plugin;
use DecodeLabs\Zest\Config\Vite as ViteConfig;

class Controller extends GenericController implements ControllerInterface
{
    #[Plugin]
    public Project $project;

    public function __construct(
        ?Dir $dir = null
    ) {
        $this->project = new Project($dir);
    }



    /**
     * Check in project
     */
    public function checkProject(): void
    {
        if (!$this->project->isInitialised()) {
            throw Exceptional::Runtime(
                message: 'Not running within a node.js project'
            );
        }
    }


    /**
     * Get controller
     */
    public function getController(): Controller
    {
        return $this;
    }

    public function loadConfig(
        ?string $name = null
    ): ViteConfig {
        return new ViteConfig($this, $name);
    }
}
