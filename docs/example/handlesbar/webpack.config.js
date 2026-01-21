const path = require('path');

module.exports = {
  mode: 'production',
  entry: {
    handlesbar: './src/server',
  },
  output: {
    path: path.resolve(__dirname, 'public/build'),
    filename: '[name].bundle.js',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
      {
        test: /\.handlebars$/,
        exclude: /node_modules/,
        use: 'handlebars-loader',
      },
    ],
  },
};
