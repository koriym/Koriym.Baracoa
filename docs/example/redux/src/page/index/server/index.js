import render from './render';

window.__SERVER_SIDE_MARKUP__ = render(window.__PRELOADED_STATE__, window.__SSR_METAS__);
