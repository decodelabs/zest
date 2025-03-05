# Zest

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/zest?style=flat)](https://packagist.org/packages/decodelabs/zest)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/zest.svg?style=flat)](https://packagist.org/packages/decodelabs/zest)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/zest.svg?style=flat)](https://packagist.org/packages/decodelabs/zest)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/zest/integrate.yml?branch=develop)](https://github.com/decodelabs/zest/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/zest?style=flat)](https://packagist.org/packages/decodelabs/zest)

### Vite front end dev environment integration

Zest provides a simplified and opinionated PHP oriented entry point to the Vite development environment.

_Get news and updates on the [DecodeLabs blog](https://blog.decodelabs.com)._

---

## Installation

Install via Composer:

```bash
composer require decodelabs/zest
```

## Usage

Zest aims to provide a simple automated way to integrate the [Vite](https://vitejs.dev/) dev server into your PHP application.

All terminal commands assume you have [Effigy](https://github.com/decodelabs/effigy) installed and working.

```bash
cd my-project
effigy zest init
```

This command will initialise a Vite config file, install everything you initially need in a package.json file and run the dev server. `ctrl+c` to quit the server.

From then on:

```bash
# Run the dev server
effigy zest dev

# Or build the static files
effigy zest build
```

Build will trigger automatically when the dev server is closed.


### Existing projects

If installing in an existing vite project, make sure to install the Zest plugin:

```bash
npm install -D @decodelabs/vite-plugin-zest
```

Then add it to your Vite config:

```javascript
import zest from '@decodelabs/vite-plugin-zest'
import { defineConfig } from 'vite'

export default defineConfig({
    plugins: [
        zest({
            buildOnExit: true
        })
    ],
})
```



### View consumption

To make use of Zest, you will need to consume the generated assets from the manifest in your views.
As it stands, there are no pre-built view adapters (there are many different view libraries out there!!), however you can adapt the one you use like this:

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
