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
use DecodeLabs\Clip\Task as TaskInterface;
use DecodeLabs\Glitch;
use DecodeLabs\Terminus;
use DecodeLabs\Veneer;
use DecodeLabs\Zest;

class Hub extends ClipHub
{
    public function initializePlatform(): void
    {
        parent::initializePlatform();

        /** @phpstan-ignore-next-line */
        Archetype::map(TaskInterface::class, Task::class);

        $controller = new Controller();
        $this->context->container->bindShared(ControllerInterface::class, $controller);
        $this->context->container->bindShared(Controller::class, $controller);

        /*
        set_exception_handler(function ($e) use ($controller) {
            if ($controller->isLocal()) {
                Glitch::handleException($e);
            } else {
                Terminus::newLine();
                Terminus::error($e->getMessage());
                Terminus::newLine();
            }
        });
        */

        Veneer\Manager::getGlobalManager()->register(
            Controller::class,
            Zest::class
        );
    }
}
