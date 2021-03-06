const path = require('path');
// eslint-disable-next-line
const webpack = require('webpack');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const Dotenv = require('dotenv-webpack');

module.exports = {
  mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',

  entry: {
    app: ['./static/js/app.js', './static/scss/app.scss', './src/Blocks/style.scss'],
    admin: ['./static/js/admin.js', './static/scss/admin.scss'],
    blocks: ['./src/Blocks/Blocks.js', './src/Blocks/editor.scss'],
  },

  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/wp-content/themes/salon/dist/',
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: 'babel-loader',
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'sass-loader',
            options: {
              implementation: require('sass'),
              sourceMap: true,
            },
          },
        ],
      },
      {
        test: /\.modernizrrc\.js$/,
        loader: 'webpack-modernizr-loader',
      },
      {
        test: /\.(png|svg|jpg|gif)$/,
        loader: 'file-loader',
        options: {
          name: '[name].[ext]',
          outputPath: 'img',
        },
      },
      {
        test: /\.(woff|woff2|eot|ttf|otf)$/,
        loader: 'file-loader',
        options: {
          name: '[name].[ext]',
          outputPath: 'fonts',
        },
      },
    ],
  },

  resolve: {
    alias: {
      modernizr$: path.resolve(__dirname, '.modernizrrc.js'),
    },
  },

  plugins: [
    // https://www.npmjs.com/package/dotenv-webpack
    new Dotenv(),
    // https://www.npmjs.com/package/browser-sync-webpack-plugin
    new BrowserSyncPlugin({
      host: 'localhost',
      port: 3000,
      proxy: 'https://salonn.local',
      files: ['dist/**/*.+(css|js)', '*.php', 'templates/**/*.twig'],
      open: false,
    }),
    // https://www.npmjs.com/package/mini-css-extract-plugin
    new MiniCssExtractPlugin({
      filename: '[name].css',
      chunkFilename: '[id].css',
    }),
  ],

  devtool: process.env.NODE_ENV === 'production' ? 'source-map' : 'inline-source-map',
};
