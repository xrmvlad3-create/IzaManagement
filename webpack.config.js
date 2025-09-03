const Encore = require('@symfony/webpack-encore');
const webpack = require("webpack");

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
    // Aceasta este forma corectă, fără opțiuni de excludere
    .enableTypeScriptLoader()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureDevServerOptions(x => {
        x.server = {
            type: 'https',
        };
        x.host = '0.0.0.0';
        x.port = '8080';
        x.hot = true;
        x.watchFiles = {
            paths: ['src/**/*.php', 'templates/**/*'],
        };
        x.allowedHosts = 'all'
    })

    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: '3.38'
    })

    .addPlugin(new webpack.DefinePlugin({
        'process.env.REACT_APP_SECURE_LOCAL_STORAGE_HASH_KEY': JSON.stringify('w7VPCVigoNFwyjlsSKkkjOW8XFh78T4O'),
        'process.env.REACT_APP_BACKEND_URL': JSON.stringify('https://localhost:8000'),
        'process.env.REACT_APP_BACKEND_API_URL': JSON.stringify('https://localhost:8000/api/'),
    }))
;

module.exports = Encore.getWebpackConfig();
