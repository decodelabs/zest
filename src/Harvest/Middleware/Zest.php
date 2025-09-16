<?php

/**
 * @package Zest
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Harvest\Middleware;

use DecodeLabs\Coercion;
use DecodeLabs\Harvest\Middleware as HarvestMiddleware;
use DecodeLabs\Harvest\MiddlewareGroup;
use DecodeLabs\Harvest\Response\Stream as StreamResponse;
use DecodeLabs\Iota;
use DecodeLabs\Monarch;
use DecodeLabs\Typify\Detector as TypeDetector;
use DecodeLabs\Zest\Config;
use DecodeLabs\Zest\Config\Generic as GenericConfig;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Psr\Http\Server\RequestHandlerInterface as PsrHandler;

class Zest implements HarvestMiddleware
{
    public MiddlewareGroup $group {
        get => MiddlewareGroup::Generator;
    }

    public int $priority {
        get => -10;
    }

    /**
     * @var array<string,Config>
     */
    protected array $configs;

    /**
     * @param ?array<string,string|Config> $configs
     */
    public function __construct(
        Iota $iota,
        ?array $configs = null,
    ) {
        $repo = $iota->loadStatic('zest');

        if ($configs === null) {
            if (
                $repo->has('__manifest') &&
                !$repo->mutable
            ) {
                // Fetch manifest if in production
                /** @var array<string> */
                $list = Coercion::asArray($repo->return('__manifest'));
            } elseif ($repo->mutable) {
                // Scan for configs
                $list = $repo->scan(function ($file) {
                    return $file !== '__manifest';
                });

                $list = iterator_to_array($list);
                sort($list);
                $export = var_export($list, true);

                $code = <<<PHP
                    <?php
                    return $export;
                    PHP;

                if ($code !== $repo->fetch('__manifest')) {
                    $repo->store('__manifest', $code);
                }
            }

            $configs = [];

            foreach ($list ?? [] as $name) {
                $configs[$name] = $repo->returnAsType(
                    key: $name,
                    type: Config::class
                );
            }
        } else {
            // Resolve config names
            foreach ($configs as $key => $config) {
                if (is_string($config)) {
                    $filename = $config === 'default' ?
                        'vite.config.php' :
                        'vite.' . $config . '.config.php';

                    $configs[$key] = $repo->return($filename);
                }
            }
        }

        /** @var array<string,Config> $configs */
        $this->configs = $configs;
    }

    public function process(
        PsrRequest $request,
        PsrHandler $next
    ): PsrResponse {
        if ($response = $this->handleAsset($request)) {
            return $response;
        }

        return $next->handle($request);
    }

    protected function handleAsset(
        PsrRequest $request
    ): ?PsrResponse {
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

        // Public dir
        $paths = [
            $root . '/' . $config->publicDir . '/' . $path
        ];

        // If not merged to public, also check outDir
        if (!str_starts_with($config->outDir, $config->publicDir)) {
            $paths[] = $root . '/' . $config->outDir . '/' . $path;
        }

        $detector = new TypeDetector();

        foreach ($paths as $path) {
            if (is_file($path)) {
                return new StreamResponse(
                    body: $path,
                    headers: [
                        'Content-Type' => $detector->detect($path),
                        'Cache-Control' => 'public, max-age=31536000'
                    ]
                );
            }
        }




        $path = $root . '/' . $config->publicDir . '/' . $path;

        if (is_file($path)) {
            return new StreamResponse(
                body: $path,
                headers: [
                    'Content-Type' => $detector->detect($path),
                    'Cache-Control' => 'public, max-age=31536000'
                ]
            );
        }

        return null;
    }

    protected function getConfigForPath(
        string &$path
    ): ?Config {
        foreach ($this->configs as $config) {
            // OutDir in public - map paths
            if (str_starts_with($config->outDir, $config->publicDir)) {
                $check = substr($config->outDir, strlen($config->publicDir));

                if (str_starts_with($path, $check)) {
                    $path = ltrim($path, '/');
                    return $config;
                }
            }

            // Test urlPrefix against path
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

        $rootPath = Monarch::getPaths()->run;

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
