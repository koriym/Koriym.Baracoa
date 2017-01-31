module.exports = {
  entry: {
    index: './src/page/index/client',
    index_ssr: './src/page/index/server',
  },
  output: {
    path: 'public/build',
    filename: '[name].bundle.js',
  },
  plugins: [
  ],
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
