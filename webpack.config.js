const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');
const dotenv = require('dotenv');

// Load environment variables from .env.local
const env = dotenv.config({ path: '.env.local' });
if (env.error) {
    console.warn('Warning: .env.local file not found or invalid');
}

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

    // Add environment variables to React build
    .configureDefinePlugin(options => {
        // Get all REACT_APP_ prefixed environment variables
        const reactAppEnvVars = {};

        if (env.parsed) {
            Object.keys(env.parsed).forEach(key => {
                if (key.startsWith('REACT_APP_')) {
                    reactAppEnvVars[`process.env.${key}`] = JSON.stringify(env.parsed[key]);
                }
            });
        }

        // Merge with existing options
        Object.assign(options, reactAppEnvVars);
    })

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
