const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .setManifestKeyPrefix('build/')
    .enableReactPreset()
    .addEntry("app", "./assets/app.tsx")
    .splitEntryChunks()
    .enableTypeScriptLoader()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .addPlugin(new webpack.DefinePlugin({
        'process.env.REACT_APP_SECURE_LOCAL_STORAGE_HASH_KEY': 'w7VPCVigoNFwyjlsSKkkjOW8XFh78T4O',
        'process.env.REACT_APP_BACKEND_URL': JSON.stringify('https://localhost:8000'),
        'process.env.REACT_APP_BACKEND_API_URL': JSON.stringify('https://localhost:8000/api/'),
    }))

    .configureDevServerOptions(options => {
        options.server = {
            type: 'https',
        };
        options.host = '0.0.0.0';
        options.port = '8080';
        options.hot = true;
        options.watchFiles = {
            paths: ['src/**/*.php', 'templates/**/*'],
        };
        options.allowedHosts = 'all';
    })

    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: '3.38'
    })
;

const config = Encore.getWebpackConfig();

// Additional configuration for backend environment variables
config.plugins.push(
    new webpack.DefinePlugin({
        'process.env.APP_ENV': JSON.stringify(process.env.APP_ENV || 'dev'),
        'process.env.APP_SECRET': JSON.stringify(process.env.APP_SECRET || ''),
    })
);

module.exports = config;
