const mix = require('laravel-mix');
const glob = require('glob');
const path = require('path');

mix.setPublicPath('./public');


// --- CSS ---------------------------------------------------

const scssPages = [
    ...glob.sync('./src/scss/_build/**/*.scss'),
];

scssPages.forEach((file) => {
    if (path.basename(file).charAt(0) !== '_') {
        mix.sass(file, file.replace('./src/scss/_build/', './css/')
            .replace('.scss', '.min.css')
        );
    }
});


// --- JavaScript --------------------------------------------

mix.js(
    [
        ...glob.sync('./src/js/modules/**/*.js'),
        './src/js/ajax.js',
        './src/js/interface.js',
    ],
    './public/js/interface.min.js'
);

const jsPages = [
    ...glob.sync('./src/js/pages/**/*.js'),
];

jsPages.forEach((file) => {
   mix.js(file, file.replace('./src/js/', './js/')
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
    )
}