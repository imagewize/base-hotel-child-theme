const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');

module.exports = (env, argv) => {
  const isDevelopment = argv.mode === 'development';

  return {
    entry: {
      app: './src/js/app.js'
    },
    output: {
      filename: '[name].js',
      path: path.resolve(__dirname, 'build'),
      clean: true
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env']
            }
          }
        },
        {
          test: /\.scss$/,
          use: [
            MiniCssExtractPlugin.loader,
            'css-loader',
            'sass-loader'
          ]
        }
      ]
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: 'main.css'  // Changed from [name].css to main.css
      }),
      new CopyPlugin({
        patterns: [
          { 
            from: 'src/fonts', 
            to: 'fonts'
          }
        ]
      })
    ],
    optimization: {
      minimize: !isDevelopment,
      minimizer: [
        new CssMinimizerPlugin(),
        new TerserPlugin()
      ]
    },
    devtool: isDevelopment ? 'source-map' : false
  };
};