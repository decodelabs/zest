<?php
/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

use DecodeLabs\Genesis;
use DecodeLabs\Zest\Hub;

require_once 'vendor/autoload.php';

Genesis::initialize(Hub::class, [
    'analysis' => true
]);
