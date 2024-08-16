const mix = require('laravel-mix');

mix.setPublicPath('./dist/');

mix
    .js(__dirname + '/resources/assets/js/app.js', 'dist/js/activity-log.js')
    .scripts(__dirname + '/node_modules/select2/dist/js/select2.full.js', 'dist/js/vendor.js')
    .styles(__dirname + '/node_modules/select2/dist/css/select2.css', 'dist/css/vendor.css')
    .sass(__dirname + '/resources/assets/sass/app.scss', 'dist/css/activity-log.css');

if (mix.inProduction()) {
    mix.version();
}
