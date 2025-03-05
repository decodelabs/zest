<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Harvest\Middleware;

use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\Harvest;
use DecodeLabs\Typify;
use DecodeLabs\Zest\Config;
use DecodeLabs\Zest\Config\Generic as GenericConfig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class Zest implements Middleware
{
    /**
     * @var array<string,Config>
     */
    protected array $configs;

    /**
     * @param ?array<string,Config> $configs
     */
    public function __construct(
        ?array $configs = null
    ) {
        if ($configs === null) {
            if (class_exists(Genesis::class)) {
                $path = Genesis::$hub->applicationPath;
            } else {
                $path = getcwd();
            }

            if (is_file($path . '/vite.config.php')) {
                $config = require $path . '/vite.config.php';

                if (!$config instanceof Config) {
                    throw Exceptional::UnexpectedValue(
                        message: 'Invalid vite config'
                    );
                }

                $configs = [
                    'default' => $config
                ];
            }
        }

        $this->configs = $configs ?? [];
    }

    /**
     * Process middleware
     */
    public function process(
        Request $request,
        Handler $next
    ): Response {
        if ($response = $this->handleAsset($request)) {
            return $response;
        }

        return $next->handle($request);
    }


    /**
     * Serve vite asset
     */
    protected function handleAsset(
        Request $request
    ): ?Response {
        $path = $request->getUri()->getPath();

        if (
            str_contains('../', $path) ||
            str_contains('/.vite/', $path)
        ) {
            return null;
        }

        if (!$config = $this->getConfigForPath($path)) {
            return null;
        }

        $root = rtrim($config->path, '/');

        $paths = [
            $root . '/' . $config->outDir . '/' . $path,
            $root . '/' . $config->publicDir . '/' . $path
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return Harvest::stream(
                    body: $path,
                    headers: [
                        'Content-Type' => Typify::detect($path),
                        'Cache-Control' => 'public, max-age=31536000'
                    ]
                );
            }
        }

        return null;
    }

    protected function getConfigForPath(
        string &$path
    ): ?Config {
        foreach ($this->configs as $config) {
            if (null !== ($prefix = $config->urlPrefix)) {
                $prefix = '/' . trim($prefix, '/') . '/';

                if (!str_starts_with($path, $prefix)) {
                    continue;
                }

                $path = substr($path, strlen($prefix));
            }

            $path = ltrim($path, '/');
            return $config;
        }

        return null;
    }
}
