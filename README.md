# Zest

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/zest?style=flat)](https://packagist.org/packages/decodelabs/zest)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/zest.svg?style=flat)](https://packagist.org/packages/decodelabs/zest)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/zest.svg?style=flat)](https://packagist.org/packages/decodelabs/zest)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/zest/integrate.yml?branch=develop)](https://github.com/string|int|floatdecodelabs/zest/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/zest?style=flat)](https://packagist.org/packages/decodelabs/zest)

### Vite front end dev environment integration

Zest provides ...

_Get news and updates on the [DecodeLabs blog](https://blog.decodelabs.com)._

---

## Installation

Install via Composer:

```bash
composer require decodelabs/zest
```

## Usage

Zest aims to provide a simple automated entry point to using the [Vite](https://vitejs.dev/) dev server.

All terminal commands assume you have [Effigy](https://github.com/decodelabs/effigy) installed and working.

```bash
cd my-project
effigy zest init vue legacy
```

This command will initialise a Zest config file using Vue and Vite Legacy plugins (more to be added shortly), translate that to a vite config file, install everything you initially need in a package.json file and run the dev server. `ctrl+c` to quit the server.

From then on:

```bash
# Run the dev server
effigy zest dev

# Or build the static files
effigy zest build
```

Build will trigger automatically when the dev server is closed.


### Config

Edit the `Zest.php` config file then run `effigy zest init` again to automatically adapt the vite config file.

You can add arbitrary config in the root `config` array.


```php
return [
    'host' => 'localhost',
    'port' => 5524, // randomly generate at init
    'https' => false,
    'outDir' => 'assets',
    'assetsDir' => 'zest',
    'publicDir' => 'assets',
    'urlPrefix' => 'my-theme/',
    'entry' => 'src/main.js',
    'hash' => false,
    'plugins' => [
        'vue' => null,
        'sass' => null,
        'legacy' => [
            'targets' => ['defaults', 'not IE 11'],
        ],
    ],
    'config' => [
        // custom config here
        'build' => [
            'emptyOutDir' => false,
            'cssCodeSplit' => false
        ]
    ]
];
```


### View consumption

To make use of Zest, you will need to consume the assest from the manifest.
As it stands, there are no pre-build view adapters (there are many different view libraries out there!!), however you can adapt the one you use like this:

```php
use DecodeLabs\Genesis;
use DecodeLabs\Zest\Manifest;

class ViewPlugin {

    public function apply(View $view): void {
        $manifest = Manifest::load(
            Genesis::$hub->getApplicationPath() . '/my-theme/manifest.json'
        );

        foreach ($manifest->getCssData() as $file => $attr) {
            /**
             * @var string $file - path to file
             * @var array $attr - array of tag attributes
             */
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



## Licensing

Zest is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
