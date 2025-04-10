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
use DecodeLabs\Overpass\Context as OverpassContext;
use DecodeLabs\Veneer\Plugin;
use DecodeLabs\Zest\Config\Vite as ViteConfig;

class Controller extends GenericController implements ControllerInterface
{
    #[Plugin]
    public OverpassContext $package;

    public function __construct(
        ?Dir $dir = null
    ) {
        // TODO: load implementation from container?
        $this->package = new OverpassContext($dir);
    }



    /**
     * Check in package
     */
    public function checkPackage(): void
    {
        if (!$this->package->isInPackage()) {
            throw Exceptional::Runtime(
                message: 'Not running within a node.js package'
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
