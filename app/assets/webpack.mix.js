const mix = require('laravel-mix');
const glob = require('glob');
const path = require('path');

let config = {};

if (process.env.TEST === 'demo') {
    mix.setPublicPath('./demo/public/');

    config = {
        scssPages: [
            ...glob.sync('./demo/src/scss/_build/**/*.scss'),
        ],
        scssPagesFromPath: './demo/src/scss/_build/',
        scssPagesToPath: './css/',
        jsPages: [
            ...glob.sync('./demo/src/js/pages/**/*.js'),
        ],
        jsPagesFromPath: './demo/src/js/',
        jsPagesToPath: './js/',
        jsModules: [
            ...glob.sync('./demo/src/js/modules/**/*.js'),
            './demo/src/js/ajax.js',
            './demo/src/js/interface.js',
        ],
    };
} else {
    mix.setPublicPath('../apps/frontend/web/_new');

    config = {
        scssPages: [
            ...glob.sync('./src/scss/_build/**/*.scss'),
        ],
        scssPagesFromPath: './src/scss/_build/',
        scssPagesToPath: './css/',
        jsPages: [
            ...glob.sync('./src/js/pages/**/*.js'),
        ],
        jsPagesFromPath: './src/js/',
        jsPagesToPath: './js/',
        jsModules: [
            ...glob.sync('./src/js/modules/**/*.js'),
            './src/js/ajax.js',
            './src/js/interface.js',
        ],
    };
}



// --- CSS ---------------------------------------------------

config.scssPages.forEach((file) => {
    if (path.basename(file).charAt(0) !== '_') {
        mix.sass(file, file.replace(config.scssPagesFromPath, config.scssPagesToPath)
            .replace('.scss', '.min.css')
        );
    }
});

// --- JavaScript --------------------------------------------
mix.js(config.jsModules, config.jsPagesToPath + 'interface.min.js');

config.jsPages.forEach((file) => {
    mix.js(file, file.replace(config.jsPagesFromPath, config.jsPagesToPath)
        .replace('.js', '.min.js')
    );
});


// ----------------------------------------------------------

mix.version();

mix.options({
    processCssUrls: false,
});

if (mix.inProduction() === false) {
    mix.sourceMaps(
        false,
        'inline-source-map'
    );
};