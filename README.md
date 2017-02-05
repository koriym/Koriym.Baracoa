# Baracoa

## A JavaScript server side rendering interface

**Bracoa** provides a simple interface for JavaScript server side rendering in PHP.

## Prerequisites

 * php7.1
 * [V8Js](http://php.net/v8js) 

## Installation
   
```
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

```
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

```javascript
$meta = ['title => 'awesome page':;
$html = $baracoa->render('min', ['name' => 'World'], meta);
```

### The Client Side


We need to do is grab the initial state from `window.__PRELOADED_STATE__` which is rendered in server side, and pass it to our `createStore()` function as the initial state.

```php
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

Use [custom startup snapshots](http://v8project.blogspot.jp/2015/09/custom-startup-snapshots.html) to boost V8 performance.
See more detail in this blog post.

* [20x performance boost with V8Js snapshots](http://stesie.github.io/2016/02/snapshot-performance)

## Run demo

### handlebar

```
git clone git@github.com:koriym/Koriym.Baracoa.git
cd Koriym.Baracoa
composer install
cd docs/example/handlesbar
yarn install
yarn run build
yarn start
```

### redux react

```
git clone git@github.com:koriym/Koriym.Baracoa.git
cd Koriym.Baracoa
composer install
cd docs/example/redux-react
yarn install
yarn run build
yarn start
```


## Install V8Js

### OSX

```
brew update
brew install homebrew/php/php71-v8js
```

edit `php.ini` or add 'V8Js.ini'

```
extension="/usr/local/opt/php71-v8js/v8js.so"
```

