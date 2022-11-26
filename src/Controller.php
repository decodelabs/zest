<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

use DecodeLabs\Archetype;
use DecodeLabs\Atlas\Dir;
use DecodeLabs\Clip\Controller as ControllerInterface;
use DecodeLabs\Clip\Controller\Generic as GenericController;
use DecodeLabs\Exceptional;
use DecodeLabs\Overpass\Context as OverpassContext;
use DecodeLabs\Veneer\LazyLoad;
use DecodeLabs\Veneer\Plugin;
use DecodeLabs\Zest\Config\Generic as GenericConfig;
use DecodeLabs\Zest\Plugin as PluginInterface;

#[LazyLoad]
class Controller extends GenericController implements ControllerInterface
{
    #[Plugin]
    public Config $config;

    #[Plugin]
    public OverpassContext $package;

    public function __construct(
        ?Dir $dir = null,
        ?Config $config = null
    ) {
        // TODO: load implementation from container?
        $this->package = new OverpassContext($dir);
        $this->config = $config ?? new GenericConfig($this);
    }



    /**
     * Check in package
     */
    public function checkPackage(): void
    {
        if (!$this->package->isInPackage()) {
            throw Exceptional::Runtime('Not running within a node.js package');
        }
    }

    /**
     * Get plugin
     */
    public function getPlugin(string $name): PluginInterface
    {
        $class = Archetype::resolve(PluginInterface::class, ucfirst($name));
        return new $class();
    }

    /**
     * Get controller
     */
    public function getController(): Controller
    {
        return $this;
    }
}
