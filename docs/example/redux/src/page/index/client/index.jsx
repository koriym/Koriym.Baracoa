import React from 'react';
import { hydrateRoot } from 'react-dom/client';
import { Provider } from 'react-redux';
import configureStore from '../store/configureStore';
import App from '../containers/App';

const preloadedState = window.__PRELOADED_STATE__;

const store = configureStore(preloadedState);

hydrateRoot(
  document.getElementById('root'),
  <Provider store={store}>
    <App />
  </Provider>,
);
