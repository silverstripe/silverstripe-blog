const Path = require('path');
const webpack = require('webpack');
// Import the core config
const webpackConfig = require('@silverstripe/webpack-config');
const {
    resolveJS,
    externalJS,
    moduleJS,
    pluginJS,
    moduleCSS,
    pluginCSS,
} = webpackConfig;

const ENV = process.env.NODE_ENV;
const PATHS = {
    ROOT: Path.resolve(),
    MODULES: 'node_modules',
    FILES_PATH: '../',
    THIRDPARTY: 'thirdparty',
    SRC: Path.resolve('client/src'),
    DIST: Path.resolve('client/dist'),
};

const config = [
    {
        name: 'bundle',
        entry: {
            main: `${PATHS.SRC}/main.js`
        },
        output: {
            path: PATHS.DIST,
            filename: 'js/[name].bundle.js',
        },
        devtool: (ENV !== 'production') ? 'source-map' : '',
        resolve: resolveJS(ENV, PATHS),
        externals: externalJS(ENV, PATHS),
        module: moduleJS(ENV, PATHS),
        plugins: pluginJS(ENV, PATHS),
    },
    {
        name: 'bundle',
        entry: {
            main: `${PATHS.SRC}/main.scss`
        },
        output: {
            path: PATHS.DIST,
            filename: 'styles/[name].css'
        },
        devtool: (ENV !== 'production') ? 'source-map' : '',
        module: moduleCSS(ENV, PATHS),
        plugins: pluginCSS(ENV, PATHS),
    },
];

module.exports = config;
