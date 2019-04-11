var elixir = require('laravel-elixir');
require('laravel-elixir-sass-compass');
/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.compass("style.scss", "public/static/web/css", {
        config_file: "public/static/web/compass/config.rb",
        sass: "public/static/web/compass/scss",
        /*sourcemap: true,*/
        style: "nested",
        font: "public/static/web/css/fonts",
        image: "public/static/web/images",
    });
});
