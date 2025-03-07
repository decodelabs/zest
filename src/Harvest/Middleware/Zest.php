<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Harvest\Middleware;

use DecodeLabs\Coercion;
use DecodeLabs\Genesis;
use DecodeLabs\Harvest;
use DecodeLabs\Iota;
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
     * @param ?array<string,string|Config> $configs
     */
    public function __construct(
        ?array $configs = null
    ) {
        if ($configs === null) {
            $iota = Iota::loadStatic('zest');

            if(
                $iota->has('__manifest') &&
                !$iota->mutable
            ) {
                // Fetch manifest if in production
                /** @var array<string> */
                $list = Coercion::asArray($iota->return('__manifest'));
            } elseif($iota->mutable) {
                // Scan for configs
                $list = $iota->scan(function($file) {
                    return $file !== '__manifest';
                });

                $list = iterator_to_array($list);
                $export = var_export($list, true);

                $code = <<<PHP
                    <?php
                    return $export;
                    PHP;

                if($code !== $iota->fetch('__manifest')) {
                    $iota->store('__manifest', $code);
                }
            }

            $configs = [];

            foreach($list ?? [] as $name) {
                $configs[$name] = $iota->returnAsType(
                    key: $name,
                    type: Config::class
                );
            }
        } else {
            // Resolve config names
            foreach($configs as $key => $config) {
                if(is_string($config)) {
                    $filename = $config === 'default' ?
                        'vite.config.php' :
                        'vite.'.$config.'.config.php';

                    $configs[$key] = Iota::loadStatic('zest')->return($filename);
                }
            }
        }

        /** @var array<string,Config> $configs */
        $this->configs = $configs;
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
        $path = urldecode($request->getUri()->getPath());

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

        if (class_exists(Genesis::class)) {
            $rootPath = Genesis::$hub->applicationPath;
        } elseif (!$rootPath = getcwd()) {
            return null;
        }

        if (!is_dir($rootPath . '/public')) {
            return null;
        }

        $path = ltrim($path, '/');

        return new GenericConfig(
            path: $rootPath,
            outDir: '@__out_of_scope__@',
            publicDir: 'public'
        );
    }
}
