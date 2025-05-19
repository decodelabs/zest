<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Clip\Controller as ControllerInterface;
use DecodeLabs\Clip\Controller\Commandment as CommandmentController;
use DecodeLabs\Exceptional;
use DecodeLabs\Overpass\Project;
use DecodeLabs\Veneer\Plugin;
use DecodeLabs\Zest\Config\Vite as ViteConfig;

class Controller extends CommandmentController implements ControllerInterface
{
    #[Plugin]
    public Project $project;

    public function __construct(
        ?Dir $dir = null
    ) {
        parent::__construct();
        $this->project = new Project($dir);
    }

    public function checkProject(): void
    {
        if (!$this->project->isInitialised()) {
            throw Exceptional::Runtime(
                message: 'Not running within a node.js project'
            );
        }
    }

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
