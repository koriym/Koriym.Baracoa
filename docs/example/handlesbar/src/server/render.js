import greetingTemplate from '../template/greeting.handlebars';

const render = (preloadedState) => {
  const root = greetingTemplate(preloadedState);
  return `<!doctype html>
<html>
  <head>
    <title></title>
  </head>
  <body>
    <div id="root">${root}</div>
  </body>
</html>
`;
};

export default render;
