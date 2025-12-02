# Zest — Package Specification

> **Cluster:** `frontend`
> **Language:** `php`
> **Milestone:** `m4`
> **Repo:** `https://github.com/decodelabs/zest`
> **Role:** Vite integration

## Overview

### Purpose

Zest provides a simplified and opinionated PHP-oriented entry point to the Vite development environment. It automates the integration of Vite's dev server and build process into PHP applications, providing manifest generation, configuration management, and asset serving capabilities.

Key features:
- **Vite integration**: Automated setup and management of Vite dev server and builds
- **Manifest generation**: Generate PHP-friendly manifests for dev and production modes
- **Configuration management**: Load and manage Vite configuration from JavaScript/TypeScript config files
- **Asset serving**: Middleware for serving Vite assets in PHP applications
- **CLI commands**: Command-line tools for initializing, running dev server, and building assets
- **View integration**: Decorators for integrating Zest manifests into view systems

### Non-Goals

- Zest does not provide Vite itself (requires Node.js and npm).
- It does not provide view adapters (users must integrate manifests into their view systems).
- It does not handle asset bundling or optimization (delegated to Vite).
- It does not provide hot module replacement (HMR) implementation (handled by Vite).
- It does not provide asset minification or compression (handled by Vite).

## Role in the Ecosystem

### Cluster & Positioning

Zest belongs to the **frontend** cluster, providing integration between PHP applications and the Vite frontend build tool. It serves as a bridge between PHP's backend and Vite's frontend development environment, enabling seamless asset management and development workflows.

### Usage Contexts

- **Development**: Running Vite dev server with PHP applications
- **Production**: Building and serving production assets
- **Manifest management**: Generating and consuming Vite manifests
- **Asset serving**: Serving Vite assets through PHP middleware
- **View integration**: Integrating Vite assets into view systems

## Public Surface

### Key Types

- **`Zest`** (class): Main CLI class extending `Clip`, providing Vite integration commands.

- **`Manifest`** (class): Manifest class for loading and managing Vite manifests. Handles dev and production manifest generation.

- **`Config`** (interface): Interface defining Vite configuration properties (host, port, https, paths, aliases, etc.).

- **`Config\Vite`** (class): Vite configuration loader, loading configuration from JavaScript/TypeScript config files via Node.js bridge.

- **`Config\Generic`** (class): Generic configuration implementation for programmatic configuration.

- **`Hub`** (class): Genesis hub for Zest, registering actions and initializing platform.

- **`Template`** (class): Template class for generating configuration files, extending `FileTemplate`.

- **`Action\Init`** (class): Action for initializing Zest in a project.

- **`Action\Dev`** (class): Action for running Vite dev server.

- **`Action\Build`** (class): Action for building production assets.

- **`Action\GenerateDevManifest`** (class): Action for generating dev manifest.

- **`Action\GenerateBuildManifest`** (class): Action for generating production manifest.

- **`Action\GenerateViteConfig`** (class): Action for generating Vite configuration file.

- **`Action\GeneratePackageConfig`** (class): Action for generating package.json configuration.

- **`Action\InstallDependencies`** (class): Action for installing npm dependencies.

- **`Action\Update`** (class): Action for updating npm dependencies.

- **`Action\ViteTrait`** (trait): Trait providing common Vite action functionality.

- **`Genesis\Build\Provider\Zest`** (class): Genesis build provider for Zest assets.

- **`Harvest\Middleware\Zest`** (class): Harvest middleware for serving Vite assets.

- **`Horizon\Decorator\Zest`** (class): Horizon decorator for integrating Zest manifests into pages.

### Main Entry Points

**Zest (CLI):**
- `new Zest(Project $project, Archetype $archetype, Iota $iota, Session $io)` — Constructor
- `$zest->checkProject(): void` — Check if running within Node.js project
- `$zest->loadConfig(?string $name = null): Config\Vite` — Load Vite configuration

**Manifest:**
- `Manifest::load(string|File $file): static` — Load manifest in production mode
- `Manifest::generateProduction(string|File $file, Config $config): static` — Generate production manifest
- `Manifest::generateDev(string|File $file, Config $config): static` — Generate dev manifest
- `new Manifest(string|File $file, bool $hot = false)` — Constructor
- `$manifest->addHeadJs(array $files): static` — Add head JS files
- `$manifest->getHeadJsData(): array` — Get head JS attributes
- `$manifest->addBodyJs(array $files): static` — Add body JS files
- `$manifest->getBodyJsData(): array` — Get body JS attributes
- `$manifest->addCss(array $files): static` — Add CSS files
- `$manifest->getCssData(): array` — Get CSS attributes
- `$manifest->isHot(): bool` — Check if manifest is for dev mode
- `$manifest->loadData(): Tree` — Load JSON data from manifest file
- `$manifest->save(): static` — Save cache file

**Config Interface:**
- `$config->host` — Dev server host (readonly property)
- `$config->port` — Dev server port (readonly property)
- `$config->https` — Use HTTPS (readonly property)
- `$config->path` — Project root path (readonly property)
- `$config->outDir` — Output directory (readonly property)
- `$config->assetsDir` — Assets directory (readonly property)
- `$config->publicDir` — Public directory (readonly property)
- `$config->aliases` — Path aliases (readonly property)
- `$config->urlPrefix` — URL prefix (readonly property)
- `$config->entry` — Entry point file (readonly property)
- `$config->manifestName` — Manifest file name (readonly property)

**Config\Vite:**
- `new Config\Vite(Project $project, Iota $iota, ?string $configName = null)` — Constructor
- `$config->reload(): void` — Reload configuration from file
- `$config->loadDefaults(): void` — Load default configuration values

**Config\Generic:**
- `new Config\Generic(string $path, ?string $host = null, ?int $port = null, ?bool $https = false, string $outDir = 'dist', string $assetsDir = 'assets', string $publicDir = 'public', array $aliases = [], ?string $urlPrefix = null, ?string $entry = null, string $manifestName = 'manifest.json')` — Constructor

**Harvest\Middleware\Zest:**
- `new Harvest\Middleware\Zest(Iota $iota, ?array $configs = null)` — Constructor
- `$middleware->process(PsrRequest $request, PsrHandler $next): PsrResponse` — Process request

**Horizon\Decorator\Zest:**
- `new Horizon\Decorator\Zest(Iota $iota)` — Constructor
- `$decorator->decorate(Page $page, string|Manifest|null $manifest = null): void` — Decorate page with manifest

**Genesis\Build\Provider\Zest:**
- `new Genesis\Build\Provider\Zest()` — Constructor
- `$provider->scanBuildItems(Dir $rootDir): Generator` — Scan build items

## Dependencies

### Decode Labs

- **archetype**: For CLI command structure
- **clip**: For CLI framework
- **coercion**: For type coercion
- **collections**: For data structures (Tree)
- **commandment**: For action framework
- **exceptional**: For error handling
- **genesis**: For build system integration
- **hatch**: For template generation
- **iota**: For static repository access
- **lucid**: For type definitions
- **monarch**: For service location and path management
- **overpass**: For Node.js project management
- **terminus**: For terminal I/O

### External

- **PHP**: See `composer.json` for supported PHP versions.
- **Node.js**: Required for Vite and npm operations.
- **npm**: Required for package management.
- **Vite**: Required runtime dependency (installed via npm).
- **@decodelabs/vite-plugin-zest**: Required Vite plugin (installed via npm).

## Behaviour & Contracts

### Invariants

- Zest requires a Node.js project (checked via `Project::isInitialised()`).
- Vite configuration loaded from JavaScript/TypeScript config files.
- Manifest files generated in `outDir` directory.
- Dev manifest includes Vite client and entry point.
- Production manifest generated from Vite build manifest.
- Config properties accessible via readonly properties.
- Manifest cache files generated as PHP files for performance.

### Input & Output Contracts

**Configuration Loading:**
- `loadConfig()` searches for config files: `vite.config.{ts,mjs,cjs,js}` or `vite.{name}.config.{ts,mjs,cjs,js}`.
- Config loaded via Node.js bridge using `load-vite-config.cjs` script.
- Config properties extracted from Vite config object.
- PHP config files (`.php` extension) loaded directly if available.
- Default values used if config properties not specified.

**Manifest Generation:**
- Production manifest generated from Vite build manifest JSON.
- Dev manifest generated with Vite client and entry point URLs.
- Manifest files saved as PHP cache files for performance.
- Manifest cache files include asset paths and attributes.
- CSS files extracted from manifest entries.
- JS files categorized as head or body scripts.

**Dev Server:**
- Dev server started via `Project::runPackage('vite', ...)`.
- Config file passed via `--config` argument.
- Server runs until interrupted (Ctrl+C).
- Dev manifest generated automatically.

**Build:**
- Build triggered via `Project::runPackage('vite', 'build', ...)`.
- Config file passed via `--config` argument.
- `--emptyOutDir` flag supported for clearing output directory.
- Production manifest generated after build.

**Asset Serving:**
- Middleware serves assets from `publicDir` and `outDir`.
- Assets matched by URL path.
- Content-Type detected via Typify.
- Cache-Control headers set for production assets.
- Path prefix matching supported via `urlPrefix`.

**View Integration:**
- Decorator loads manifest from Iota repository or file path.
- Manifest CSS added as link tags.
- Manifest JS added as script tags (head or body).
- URLs normalized (absolute URLs preserved, relative URLs prefixed with `/`).

## Error Handling

- **Project not initialized**: `Exceptional::Runtime` thrown if not running within Node.js project.
- **Node modules not found**: `Exceptional::Runtime` thrown if `node_modules` directory not found.
- **Config file not found**: Default values used if config file not found.
- **Manifest file not found**: Empty manifest returned if manifest file not found.
- **Invalid alias path**: `Exceptional::Runtime` thrown if alias path cannot be resolved.
- **Asset not found**: Middleware returns `null` if asset not found (request passed to next handler).

## Configuration & Extensibility

### Vite Configuration

Vite configuration loaded from JavaScript/TypeScript config files:

```javascript
import { defineConfig } from 'vite'

export default defineConfig({
    server: {
        host: 'localhost',
        port: 3000,
        https: false
    },
    build: {
        outDir: 'dist',
        assetsDir: 'assets'
    },
    publicDir: 'public',
    resolve: {
        alias: {
            '@': '/src'
        }
    },
    base: '/',
    build: {
        rollupOptions: {
            input: 'src/main.js'
        },
        manifest: 'manifest.json'
    }
})
```

### PHP Configuration

PHP configuration files (`.php` extension) can be used instead:

```php
<?php
use DecodeLabs\Zest\Config\Generic;

return new Generic(
    path: '/path/to/project',
    host: 'localhost',
    port: 3000,
    https: false,
    outDir: 'dist',
    assetsDir: 'assets',
    publicDir: 'public',
    aliases: ['@' => '/src'],
    urlPrefix: '/',
    entry: 'src/main.js',
    manifestName: 'manifest.json'
);
```

### Multiple Configurations

Multiple Vite configurations supported via config name:

```bash
effigy zest dev --config=admin
effigy zest build --config=admin
```

Config files: `vite.admin.config.ts`, `vite.admin.config.js`, etc.

### Custom Manifest Path

Manifest path can be specified when loading:

```php
$manifest = Manifest::load('/path/to/manifest.json');
```

## Interactions with Other Packages

- **Genesis**: Provides build provider for Zest assets (`Genesis\Build\Provider\Zest`).
- **Harvest**: Provides middleware for serving Vite assets (`Harvest\Middleware\Zest`).
- **Horizon**: Provides decorator for integrating Zest manifests into pages (`Horizon\Decorator\Zest`).
- **Monarch**: Used for service location and path management.
- **Iota**: Used for static repository access (config and manifest storage).
- **Overpass**: Used for Node.js project management and npm operations.
- **Typify**: Used for MIME type detection in asset serving.

## Usage Examples

### Initialization

```bash
cd my-project
effigy zest init
```

This initializes Vite config, installs dependencies, and runs the dev server.

### Development Server

```bash
effigy zest dev
```

Runs Vite dev server. Use `--config=name` for multiple configurations.

### Production Build

```bash
effigy zest build
```

Builds production assets. Use `--emptyOutDir` to clear output directory first.

### Manifest Loading

```php
use DecodeLabs\Zest\Manifest;

$manifest = Manifest::load('/path/to/manifest.json');

foreach ($manifest->getCssData() as $file => $attrs) {
    echo '<link rel="stylesheet" href="' . $file . '">';
}

foreach ($manifest->getHeadJsData() as $file => $attrs) {
    echo '<script src="' . $file . '"></script>';
}

foreach ($manifest->getBodyJsData() as $file => $attrs) {
    echo '<script src="' . $file . '"></script>';
}
```

### View Integration

```php
use DecodeLabs\Monarch;
use DecodeLabs\Zest\Manifest;

class ViewPlugin {
    public function apply(View $view): void {
        $manifest = Manifest::load(
            Monarch::getPaths()->root . '/my-theme/manifest.json'
        );

        foreach ($manifest->getCssData() as $file => $attr) {
            $view->addCss($file, $attr);
        }

        foreach ($manifest->getHeadJsData() as $file => $attr) {
            $view->addHeadJs($file, $attr);
        }

        foreach ($manifest->getBodyJsData() as $file => $attr) {
            $view->addFootJs($file, $attr);
        }

        if ($manifest->isHot()) {
            $view->addBodyClass('zest-dev preload');
        }
    }
}
```

### Harvest Middleware

```php
use DecodeLabs\Harvest\Middleware\Zest;
use DecodeLabs\Iota;

$middleware = new Zest(
    iota: $iota,
    configs: [
        'default' => 'vite.config.php',
        'admin' => 'vite.admin.config.php'
    ]
);
```

### Horizon Decorator

```php
use DecodeLabs\Horizon\Decorator\Zest;
use DecodeLabs\Iota;

$decorator = new Zest($iota);
$decorator->decorate($page, 'vite.config.php');
```

### Configuration Loading

```php
use DecodeLabs\Zest;

$zest = new Zest($project, $archetype, $iota, $io);
$config = $zest->loadConfig('admin');

echo $config->host; // 'localhost'
echo $config->port; // 3000
echo $config->outDir; // 'dist'
```

### Manifest Generation

```php
use DecodeLabs\Zest\Manifest;
use DecodeLabs\Zest\Config\Vite;

$config = new Vite($project, $iota);

// Generate dev manifest
$devManifest = Manifest::generateDev(
    '/path/to/manifest.json',
    $config
);

// Generate production manifest
$prodManifest = Manifest::generateProduction(
    '/path/to/manifest.json',
    $config
);
```

## Implementation Notes (for Contributors)

### Zest Implementation

- Zest extends `Clip` for CLI command structure.
- Uses `Project` from Overpass for Node.js project management.
- Config loaded via Node.js bridge using `load-vite-config.cjs` script.
- Config properties extracted from Vite config object using Tree navigation.

### Manifest Implementation

- Manifest loads JSON data from Vite manifest file.
- Production manifest extracts entry points and CSS from Vite build manifest.
- Dev manifest generates URLs for Vite client and entry point.
- Manifest cache files generated as PHP files for performance.
- Cache files include asset paths and attributes as PHP arrays.

### Config Implementation

- `Config\Vite` loads configuration from JavaScript/TypeScript files via Node.js bridge.
- Config properties extracted using Tree navigation.
- PHP config files (`.php` extension) loaded directly if available.
- Default values used if config properties not specified.
- `Config\Generic` provides programmatic configuration.

### Action Implementation

- Actions extend `Commandment\Action` interface.
- `ViteTrait` provides common functionality (config loading, manifest file resolution).
- Actions use `Project::runPackage()` for npm/Vite operations.
- Config name passed via `--config` argument.

### Middleware Implementation

- `Harvest\Middleware\Zest` serves assets from `publicDir` and `outDir`.
- Assets matched by URL path with prefix support.
- Content-Type detected via Typify.
- Cache-Control headers set for production assets.

### Decorator Implementation

- `Horizon\Decorator\Zest` loads manifest from Iota repository or file path.
- Manifest CSS added as link tags.
- Manifest JS added as script tags (head or body).
- URLs normalized (absolute URLs preserved, relative URLs prefixed with `/`).

### Build Provider Implementation

- `Genesis\Build\Provider\Zest` scans `public` directory for build items.
- Provides integration with Genesis build system.

## Testing & Quality

**Current Status:**
- Code quality: 4/5
- README quality: 3/5
- Documentation: 0/5 (no formal docs yet)
- Tests: 0/5 (no test suite yet)

**Testing Considerations:**
- Zest should be tested for:
  - Configuration loading (JavaScript/TypeScript and PHP)
  - Manifest generation (dev and production)
  - Dev server execution
  - Build execution
  - Asset serving (middleware)
  - View integration (decorator)
  - Multiple configurations
  - Error handling (missing files, invalid configs)

- Manifest should be tested for:
  - Loading from JSON files
  - Generating dev manifests
  - Generating production manifests
  - Cache file generation
  - Asset path extraction
  - CSS extraction
  - JS categorization (head vs body)

- Config should be tested for:
  - Loading from JavaScript/TypeScript files
  - Loading from PHP files
  - Default value handling
  - Property access
  - Multiple config files

- Actions should be tested for:
  - Initialization
  - Dev server execution
  - Build execution
  - Manifest generation
  - Config generation
  - Package config generation
  - Dependency installation

- Middleware should be tested for:
  - Asset serving
  - Path matching
  - Prefix handling
  - Content-Type detection
  - Cache headers

- Decorator should be tested for:
  - Manifest loading
  - Page decoration
  - URL normalization
  - Multiple manifests

## Roadmap & Future Ideas

- **View adapters**: Pre-built view adapters for popular view libraries
- **Asset optimization**: Integration with asset optimization tools
- **Hot reload**: Enhanced hot module replacement (HMR) support
- **Build caching**: Build result caching for faster rebuilds
- **Asset versioning**: Automatic asset versioning for cache busting
- **Multiple entry points**: Better support for multiple entry points
- **Custom plugins**: Support for custom Vite plugins
- **Performance monitoring**: Performance monitoring and metrics

## References

- Package repository: https://github.com/decodelabs/zest
- Composer package: https://packagist.org/packages/decodelabs/zest
- Vite documentation: https://vitejs.dev/
- Vite plugin: https://github.com/decodelabs/vite-plugin-zest
- Related packages:
  - Overpass: Node.js project management
  - Harvest: HTTP middleware framework
  - Horizon: Page decorator framework
  - Genesis: Build system

