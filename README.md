# Baracoa

[![CI](https://github.com/koriym/Koriym.Baracoa/actions/workflows/ci.yml/badge.svg)](https://github.com/koriym/Koriym.Baracoa/actions/workflows/ci.yml)

A JavaScript server side rendering interface for PHP.

**Baracoa** provides a simple interface for JavaScript server side rendering in PHP.

## Prerequisites

 * PHP 8.2+
 * [V8Js](http://php.net/v8js) (optional, PhpExecJs used as fallback) 

## Installation

```bash
composer require koriym/baracoa
```

## Basic

In a JS renderer application, implement `render` function which takes parameter(s) and return html string. 

```javascript
const render = state => (
  `Hello ${state.name}`
)
```

Call the `render()` method with JS app name and values to assign to redner.

```php
$baracoa = new Baracoa($jsDir, new ExceptionHandler());
$html = $baracoa->render('min', ['name' => 'World']);
echo $html; // Hello World
```

In this example, you need to place `min.bundle.js` JS file in `$jsDir ` directory.
Every page needs own JS view application which is bundled single file by bundler tool like [webpack](https://webpack.github.io/).


Typical entry file is like following code.

```javascript
import render from './render';
global.render = render;
```


In next section we see the example of Redux with React applicaiton example.


## Redux React

### The Server Side

Inject an initial component HTML and initial state into a template to be rendered on the client side.
To pass along the state, we add a `<script>` tag that will attach `preloadedState` to `window.__PRELOADED_STATE__`.
The preloadedState will then be available on the client side by accessing `window.__PRELOADED_STATE__`.

We also include our bundle file for the client-side application via a `<script>` tag.
This is whatever output your bundling tool provides for your client entry point. 


### render.js

```javascript
import React from 'react';
import { renderToString } from 'react-dom/server';
import { Provider } from 'react-redux';
import escape from 'escape-html';
import serialize from 'serialize-javascript';
import App from '../containers/App';
import configureStore from '../store/configureStore';

const render = (preloadedState, metas) => {
  const store = configureStore(preloadedState);
  const root = renderToString(
    <Provider store={store}>
      <App />
    </Provider>,
  );
  return `<!doctype html>
    <html>
      <head>
        <title>${escape(metas.title)}</title>
      </head>
      <body>
        <div id="root">${root}</div>
        <script>
          window.__PRELOADED_STATE__ = ${serialize(preloadedState)}
        </script>
        <script src="/build/index.bundle.js"></script>
      </body>
    </html>
`;
};

export default render;
```

`render()` method can pass second parameter as SSR meta data which is only available in server side rendering. Typically this value is used in `<header>` such as for OGP.

```php
$meta = ['title => 'awesome page'];
$html = $baracoa->render('min', ['name' => 'World'], $meta);
```

### The Client Side


We need to do is grab the initial state from `window.__PRELOADED_STATE__` which is rendered in server side, and pass it to our `createStore()` function as the initial state.

```javascript
import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import configureStore from '../store/configureStore';
import App from '../containers/App';

const preloadedState = window.__PRELOADED_STATE__;
const store = configureStore(preloadedState);

render(
  <Provider store={store}>
    <App />
  </Provider>,
  document.getElementById('root'),
);
```

## Performance boost 

```php
$cache = new FilesystemCache() // PSR-16
$baracoa = new CacheBaracoa($appBundleJsPath, new ExceptionHandler(), $cache);
```
An *external* "snapshot" is saved to increase performance in each app with `CacheBaracoa`.
Highly recommended in production site.
Consider *internal* snapshot for more performance.

See more detail in this blog post.

* [20x performance boost with V8Js snapshots](http://stesie.github.io/2016/02/snapshot-performance)

## Run demo

### min

```bash
git clone git@github.com:koriym/Koriym.Baracoa.git
cd Koriym.Baracoa
composer install
cd docs/example/min
php index.php
# HelloWorld
```

### handlebar

```bash
cd docs/example/handlesbar
npm install
npm run build
php public/index.php
# <!doctype html>
# ...
```

### redux react

```bash
cd docs/example/redux
npm install
npm run build
npm start
```

## Install V8Js

### macOS

Using [shivammathur/homebrew-extensions](https://github.com/shivammathur/homebrew-extensions):

```bash
brew install shivammathur/extensions/v8js@8.4
```

(Replace `8.4` with your PHP version)

### Linux

See the [V8Js README](https://github.com/phpv8/v8js) for installation instructions.

## JS UI Application Skeleton

[UiSkeleton](https://github.com/koriym/Koriym.JsUiSkeleton) is a Javascript UI application skeleton with  hot module loader, browsersync, test, lint and more for development.