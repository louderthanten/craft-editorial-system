var gulp = require('gulp');
var autoprefixer = require('autoprefixer');
var browserSync = require('browser-sync');
var nano = require('cssnano');
var cssnext = require('postcss-cssnext');
var concat  = require('gulp-concat-util');
var postcss = require('gulp-postcss');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var lost = require('lost');
var assets  = require('postcss-assets');
var gutil = require('gulp-util');


gulp.task('css', function () {
  var processors = [
    autoprefixer({browsers: ['last 2 versions']}),
    assets({loadPaths: ['public_html/img/']}),
    cssnext,
    lost,
    nano
  ];
  return gulp.src('source/scss/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(postcss(processors))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('public_html/css'))
    .pipe(browserSync.reload({stream:true}))
});

gulp.task('critical', function() {
  return gulp.src('public_html/css/app.css')
    .pipe(concat.header('<style>'))
    .pipe(concat.footer('</style>'))
    .pipe(rename({
        basename: 'critical-css',
        extname: '.html'
    }))
    .pipe(gulp.dest('craft/templates/_modules/'));
});

gulp.task('js', function () {
  return gulp.src([
      'bower_components/Lettering.js/jquery.lettering.js',
      'bower_components/fitvids/jquery.fitvids.js',
      'source/js/init/*.js'
    ])
    .pipe(sourcemaps.init())
    .pipe(concat('app.min.js'))
    .pipe(uglify().on('error', gutil.log))
    .pipe(gulp.dest('public_html/js'))
    .pipe(sourcemaps.write('.'))
    .pipe(browserSync.reload({stream:true}))
});

gulp.task('js-async', function () {
  return gulp.src([
      'bower_components/bigfoot/dist/bigfoot.js',
      'source/js/async/*.js'
    ])
    .pipe(sourcemaps.init())
    .pipe(concat('app-async.min.js'))
    .pipe(uglify().on('error', gutil.log))
    .pipe(gulp.dest('public_html/js'))
    .pipe(sourcemaps.write('.'))
    .pipe(browserSync.reload({stream:true}))
});

gulp.task('js-pages', function() {
  gulp.src('source/js/*.js')
    .pipe(uglify().on('error', gutil.log))
    .pipe(gulp.dest('public_html/js'))
});

gulp.task('watch', function () {
  gulp.watch('source/scss/**/*.scss', ['css']);
  gulp.watch('source/js/**/*.js', ['js', 'js-async']);
  gulp.watch('craft/templates/**/*.html').on('change', browserSync.reload);
});


gulp.task('browser-sync', function() {
  browserSync.init(['*.html'], {
    //proxy: 'domain.dev'
  });
});

gulp.task('start', ['watch', 'critical', 'browser-sync']);

gulp.task('default', ['css', 'js', 'js-async', 'js-pages']);
