var gulp = require('gulp');
var browserSync = require('browser-sync');
var nano = require('cssnano');
var cssnext = require('postcss-cssnext');
var concat  = require('gulp-concat-util');
var postcss = require('gulp-postcss');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var assets  = require('postcss-assets');
var gutil = require('gulp-util');
var data = require('gulp-data');
var path = require('path');


gulp.task('css', function () {
  var processors = [
    assets({loadPaths: ['public/img/']}),
    cssnext,
    nano
  ];
  return gulp.src('source/scss/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(postcss(processors))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('public/css'))
    .pipe(browserSync.reload({stream:true}))
});

gulp.task('critical', function() {
  return gulp.src('public/css/critical.css')
    .pipe(concat.header('<style>'))
    .pipe(concat.footer('</style>'))
    .pipe(rename({
        basename: 'critical',
        extname: '.html'
    }))
    .pipe(gulp.dest('craft/templates/_css'));
});

gulp.task('js', function () {
  return gulp.src([
      'bower_components/fitvids/jquery.fitvids.js',
      'source/js/components/*.js'
    ])
    .pipe(sourcemaps.init())
    .pipe(concat('app.min.js'))
    .pipe(uglify().on('error', gutil.log))
    .pipe(gulp.dest('public/js'))
    .pipe(sourcemaps.write('.'))
    .pipe(browserSync.reload({stream:true}))
});

gulp.task('js-pages', function() {
  gulp.src('source/js/*.js')
    .pipe(uglify().on('error', gutil.log))
    .pipe(gulp.dest('public/js'))
    .pipe(browserSync.reload({stream:true}))
});


gulp.task('watch', function () {
  gulp.watch('source/scss/**/*.scss', ['css']);
  gulp.watch('source/js/**/*.js', ['js']);
  gulp.watch(['craft/templates/**/*.html']).on('change', browserSync.reload);
});

//gulp.task('browser-sync', function() {
//  browserSync.init({
//    server: {
//      baseDir: "public/"
//    }
//  });
//});

// OR dynamic server
gulp.task('browser-sync', function() {
  browserSync.init(['*.html'], {
    proxy: 'editorial.dev'
  });
});

gulp.task('start', ['watch', 'critical', 'browser-sync']);

gulp.task('default', ['css', 'js', 'js-pages', 'critical']);
