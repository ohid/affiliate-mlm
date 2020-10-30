const { src, dest, series, parallel, watch } = require('gulp');
const sass = require('gulp-sass'),
      sourcemaps = require('gulp-sourcemaps'),
      terser = require('gulp-terser'),
      uglify = require('gulp-uglify'),
      uglifycss = require('gulp-uglifycss');

const files = {
    scssPath: {
        src: 'assets/scss/*.scss',
        dest: 'assets/css/'
    },
    jsPath: {
        src: 'assets/js/*.js',
        dest: 'assets/js/build'
    },
}

/**
 * SCSS Task
 * Converts the SCSS files to CSS and minify them
 */
function scssTask(){    
    return src(files.scssPath.src)
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(uglifycss())
        .pipe(sourcemaps.write('.'))
        .pipe(dest(files.scssPath.dest)
    );
}

/**
 * JS Task
 * Minifies the JS files
 */
function jsTask(){
    return src(files.jsPath.src)
        .pipe(terser())
        .pipe(dest(files.jsPath.dest)
    );
}

/**
 * Watch Task
 * Watch for changes on scss and js files
 */
function watchTask(){
    watch(
        [files.scssPath.src, files.jsPath.src],
        parallel(scssTask, jsTask)
    );
}

exports.watchTask = watchTask;
exports.default = series( scssTask, jsTask, watchTask);