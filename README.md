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

Every page rendering needs each JS application which is bundled single JS file. SPA is not that case off course.
We recommend [webpack](https://webpack.github.io/) for that.
`$jsBundleDir` is the directory of those files.

```php
$baracoa = new Baracoa($jsBundleDir, new ExceptionHandler(), new V8Js());
```

To render the UI with JS application, Call the `render()` method with JS app name and values to assign.
In this example, you need to place `handlebar.bundle.js` file in `$jsBundleDir` directory.

```php
$html = $baracoa->render('handlesbar', ['name' => 'World']);
echo $html; // Hello World
```

In JS renderer application, you take `window.__PRELOADED_STATE__` as initial state and set output(html) string to `window.__SERVER_SIDE_MARKUP__`.
To minimize to depend `Baracoa`, We provide minimum gateway code like this.
  
```javascript
import render from './render';

window.__SERVER_SIDE_MARKUP__ = render(window.__PRELOADED_STATE__);
```

Here is a minimalistic example using `handlesbar` template engine.
 
```javascript
import greetingTemplate from '../template/greeting.handlebars'; // <div>Hello {{ name }}</div>

const render = (preloadedState) => (
  greetingTemplate(preloadedState); // <div>Hello World</div>
);

export default render;
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
`window.__SSR_METAS__` data is only used in server side. It is not exposed to in HTML code unlike `window.__PRELOADED_STATE__`. 
You can also render only root dom with JS and combine page framework with PHP. 

### The Client Side


We need to do is grab the initial state from `window.__PRELOADED_STATE__`, and pass it to our createStore() function as the initial state.

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

