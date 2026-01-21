const path = require('path');

module.exports = {
  entry: {
    index: './src/page/index/client',
    index_ssr: './src/page/index/server',
  },
  output: {
    path: path.resolve(__dirname, 'public/build'),
    filename: '[name].bundle.js',
  },
  module: {
    loaders: [
      {
        test: /\.jsx?$/,
        loaders: ['babel-loader'],
        exclude: /node_modules/,
      },
    ]
  },
  resolve: {
    extensions: ['.js', '.jsx'],
  },
};
