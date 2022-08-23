/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

var gulp = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify-es').default,
    sass = require('gulp-sass'),
    plumber = require('gulp-plumber'),
    rename = require('gulp-rename'),
    cleanCSS = require('gulp-clean-css'),
    notify = require('gulp-notify'),
    sourcemaps = require('gulp-sourcemaps'),
    pump = require('pump'),
    autoprefixer = require('gulp-autoprefixer');

gulp.task('js-geolocation-attendance-control-places', function (cb)
{
    pump([
        gulp.src([
            'public/js/source/material.min.js',
            'public/js/source/mdl-select.js',
            'public/js/source/hilitor.js',
            'public/js/source/geolocation-attendance-control-places.js'
        ]),
        concat('geolocation-attendance-control-places.min.js'),
        uglify(),
        gulp.dest('public/js/'),
        notify({ message: 'Admin scripts task complete' })
    ], cb);
});

gulp.task('js-geolocation-attendance-control-activities', function (cb)
{
    pump([
        gulp.src([
            'public/js/source/material.min.js',
            'public/js/source/mdl-select.js',
            'public/js/source/hilitor.js',
            'public/js/source/geolocation-attendance-control-activities.js'
        ]),
        concat('geolocation-attendance-control-activities.min.js'),
        uglify(),
        gulp.dest('public/js/'),
        notify({ message: 'Admin scripts task complete' })
    ], cb);
});

gulp.task('js-geolocation-attendance-control-attendance', function (cb)
{
    pump([
        gulp.src([
            'public/js/source/material.min.js',
            'public/js/source/mdl-select.js',
            'public/js/source/hilitor.js',
            'public/js/source/geolocation-attendance-control-attendance.js'
        ]),
        concat('geolocation-attendance-control-attendance.min.js'),
        uglify(),
        gulp.dest('public/js/'),
        notify({ message: 'Admin scripts task complete' })
    ], cb);
});

gulp.task('js-geolocation-attendance-control-front', function (cb)
{
    pump([
        gulp.src([
            'public/js/source/hilitor.js',
            'public/js/source/geolocation-attendance-control.js'
        ]),
        concat('geolocation-attendance-control.min.js'),
        uglify(),
        gulp.dest('public/js/')
    ], cb);
});

gulp.task('js-vuetify', function (cb)
{
    pump([
        gulp.src([
            'public/js/source/external/vue.js',
            'public/js/source/external/vuetify.js'
        ]),
        concat('vuetify.min.js'),
        uglify(),
        gulp.dest('public/js/'),
        notify({ message: 'Admin scripts task complete' })
    ], cb);
});

gulp.task('css-geolocation-attendance-control-places', function ()
{

    gulp.src('public/sass/admin/geolocation-attendance-control-places.sass')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({ errLogToConsole: true, outputStyle: 'expanded' }).on('error', sass.logError))
        .pipe(sourcemaps.write({ includeContent: false }))
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(autoprefixer({ browsers: ['last 2 versions'], cascade: true }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css/source/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('public/css/'));
});

gulp.task('css-geolocation-attendance-control-activities', function ()
{

    gulp.src('public/sass/admin/geolocation-attendance-control-activities.sass')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({ errLogToConsole: true, outputStyle: 'expanded' }).on('error', sass.logError))
        .pipe(sourcemaps.write({ includeContent: false }))
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(autoprefixer({ browsers: ['last 2 versions'], cascade: true }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css/source/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('public/css/'));
});

gulp.task('css-geolocation-attendance-control-attendance', function ()
{

    gulp.src('public/sass/admin/geolocation-attendance-control-attendance.sass')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({ errLogToConsole: true, outputStyle: 'expanded' }).on('error', sass.logError))
        .pipe(sourcemaps.write({ includeContent: false }))
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(autoprefixer({ browsers: ['last 2 versions'], cascade: true }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css/source/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('public/css/'));
});

gulp.task('css-geolocation-attendance-control-front', function ()
{

    gulp.src('public/sass/front/geolocation-attendance-control.sass')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({ errLogToConsole: true, outputStyle: 'expanded' }).on('error', sass.logError))
        .pipe(sourcemaps.write({ includeContent: false }))
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(autoprefixer({ browsers: ['last 2 versions'], cascade: true }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css/source/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('public/css/'))
        .pipe(notify({ message: 'Front styles task complete' }));

});

/*
gulp.task('css-vuetify', function () {

    gulp.src([
        'public/css/source/external/google-fonts.min.css',
        'public/css/source/external/vuetify.min.css'
        ])
        .pipe(plumber())
        .pipe(autoprefixer({ browsers: ['last 2 versions'], cascade:  true }))
        .pipe(rename('vuetify.min.css'))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('public/css/'))
        .pipe(notify({ message: 'Front styles task complete' }));

});
*/

gulp.task('js', [
    'js-geolocation-attendance-control-attendance',
    'js-geolocation-attendance-control-places',
    'js-geolocation-attendance-control-activities',
    'js-geolocation-attendance-control-front',
    'js-vuetify'
]);

gulp.task('css', [
    'css-geolocation-attendance-control-attendance',
    'css-geolocation-attendance-control-places',
    'css-geolocation-attendance-control-activities',
    'css-geolocation-attendance-control-front',
    // 'css-vuetify'
]);

gulp.task('watch', function ()
{

    var sassFiles = [
        'public/sass/admin/**/*.sass',
        'public/sass/admin/*.sass',
        'public/sass/front/**/*.sass',
        'public/sass/front/geolocation-attendance-control.sass'
    ],

        jsFiles = 'public/js/source/*';

    gulp.watch(jsFiles, ['js']);

    gulp.watch(sassFiles, ['css']);

});

gulp.task('default', ['js', 'css']);
