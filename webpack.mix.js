const mix = require('laravel-mix');

mix.sourceMaps();

mix.js('resources/js/app.js', 'public/js')
 .vue();

mix.browserSync('127.0.0.1:8000');