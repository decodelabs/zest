<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Overpass\Project;
use DecodeLabs\Terminus\Session;
use DecodeLabs\Zest\Config\Vite as ViteConfig;

class Zest extends Clip
{
    public function __construct(
        protected(set) Project $project,
        protected Archetype $archetype,
        protected Iota $iota,
        protected Session $io
    ) {
        parent::__construct($archetype, $io);
    }

    public function checkProject(): void
    {
        if (!$this->project->isInitialised()) {
            throw Exceptional::Runtime(
                message: 'Not running within a node.js project'
            );
        }
    }

    public function loadConfig(
        ?string $name = null
    ): ViteConfig {
        return new ViteConfig(
            project: $this->project,
            iota: $this->iota,
            configName: $name,
        );
    }
}
