<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

require_once 'vendor/autoload.php';

use DecodeLabs\Genesis\Bootstrap\Analysis;
use DecodeLabs\Zest\Hub;

new Analysis(
    hubClass: Hub::class
)->initializeOnly();
