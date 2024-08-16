const mix = require('laravel-mix');

mix.setPublicPath('./dist/');

mix
    .js(__dirname + '/resources/assets/js/app.js', 'dist/js/activity-log.js',)
    .sass(__dirname + '/resources/assets/sass/app.scss', 'dist/css/activity-log.css');

if (mix.inProduction()) {
    mix.version();
}
