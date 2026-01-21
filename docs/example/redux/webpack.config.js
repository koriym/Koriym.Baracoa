const path = require('path');

module.exports = {
  mode: 'production',
  entry: {
    index: './src/page/index/client',
    index_ssr: './src/page/index/server',
  },
  output: {
    path: path.resolve(__dirname, 'public/build'),
    filename: '[name].bundle.js',
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env', '@babel/preset-react'],
          },
        },
      },
    ],
  },
  resolve: {
    extensions: ['.js', '.jsx'],
    fallback: {
      buffer: false,
    },
  },
};
