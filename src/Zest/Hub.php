<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

use DecodeLabs\Archetype;
use DecodeLabs\Clip\Controller as ControllerInterface;
use DecodeLabs\Clip\Hub as ClipHub;
use DecodeLabs\Commandment\Action as ActionInterface;
use DecodeLabs\Monarch;
use DecodeLabs\Pandora\Container;
use DecodeLabs\Veneer;
use DecodeLabs\Zest;

class Hub extends ClipHub
{
    public function initializePlatform(): void
    {
        parent::initializePlatform();

        // @phpstan-ignore-next-line
        Archetype::map(ActionInterface::class, Action::class);

        if(Monarch::$container instanceof Container) {
            $controller = new Controller();
            Monarch::$container->bindShared(ControllerInterface::class, $controller);
            Monarch::$container->bindShared(Controller::class, $controller);
        }

        Veneer\Manager::getGlobalManager()->register(
            Controller::class,
            Zest::class
        );
    }
}
