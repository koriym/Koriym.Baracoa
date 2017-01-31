module.exports = {
  entry: {
    handlesbar: './src/server',
  },
  output: {
    path: 'public/build',
    filename: '[name].bundle.js',
  },
  module: {
    loaders: [
      {
        test: /\.js$/,
        loaders: ['babel-loader'],
        exclude: /node_modules/,
      },
      {
        test: /\.handlebars$/,
        loaders: ['handlebars-loader'],
        exclude: /node_modules/,
      }
    ]
  }
};
