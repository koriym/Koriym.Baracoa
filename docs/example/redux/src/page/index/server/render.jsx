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
