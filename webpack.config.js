const Path = require('path');
const { JavascriptWebpackConfig, CssWebpackConfig } = require('@silverstripe/webpack-config');

const ENV = process.env.NODE_ENV;
const PATHS = {
    ROOT: Path.resolve(),
    SRC: Path.resolve('client/src'),
    DIST: Path.resolve('client/dist'),
};

const config = [
  // Main JS bundle
  new JavascriptWebpackConfig('js', PATHS, 'silverstripe/blog')
    .setEntry({
      main: `${PATHS.SRC}/main.js`
    })
    .mergeConfig({
      output: {
        filename: 'js/[name].bundle.js',
      },
    })
    .getConfig(),
  // sass to css
  new CssWebpackConfig('css', PATHS)
    .setEntry({
      main: `${PATHS.SRC}/main.scss`
    })
    .getConfig(),
];

module.exports = config;
