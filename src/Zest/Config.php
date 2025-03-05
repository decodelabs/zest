<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Zest;

interface Config
{
    public ?string $host { get; }
    public ?int $port { get; }
    public ?bool $https { get; }

    public string $path { get; }
    public string $outDir { get; }
    public string $assetsDir { get; }
    public string $publicDir { get; }

    /**
     * @var array<string,string>
     */
    public array $aliases { get; }

    public ?string $urlPrefix { get; }
    public ?string $entry { get; }
    public string $manifestName { get; }
}
