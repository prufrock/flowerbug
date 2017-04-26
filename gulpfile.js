const fs = require('fs');
const path = require('path');
const gulp = require('gulp');
const elixir = require('laravel-elixir');
const shell = require('gulp-shell');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

function getFolders(dir) {

    return fs.readdirSync(dir)
        .filter(function (file) {
            return fs.statSync(path.join(dir, file)).isDirectory();
        });
}

let paths = {
    'docs': './docs/'
};

gulp.task('docs', () => {

    let folders = getFolders(paths.docs);

    let tasks = folders.map((folder) => {

        return gulp.src(path.join(paths.docs, folder, 'uml', '*.puml'))
            .pipe(shell([
                'plantuml "<%= file.path %>"'
            ]));
    });

    return tasks;
});

elixir((mix) => {

    mix.sass('app.scss')
        .webpack('app.js');
});
