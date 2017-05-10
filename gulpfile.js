var gulp = require('gulp');
var sass = require('gulp-sass');
var cssmin = require('gulp-cssmin');
var runSequence = require('run-sequence');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var sassSources = ['src/wp-content/themes/vpn-view/scss/**/*.scss'];

gulp.task('sass-process', function() {
    return gulp.src('src/wp-content/themes/vpn-view/scss/main.scss')
        .pipe(sass())
        .pipe(concat('style.css'))
        .pipe(gulp.dest('src/wp-content/themes/vpn-view'))
});

gulp.task('css-compress', function() {
    return gulp.src('src/wp-content/themes/vpn-view/style.css')
        .pipe(cssmin())
        .pipe(concat('style.css'))
        .pipe(gulp.dest('src/wp-content/themes/vpn-view'))
});

gulp.task('css-prefix', function() {
    return gulp.src('src/wp-content/themes/vpn-view/style.css')
        .pipe(autoprefixer({
            browsers: ['IE 9', 'last 2 versions'],
            cascade: false
        }))
        .pipe(gulp.dest('src/wp-content/themes/vpn-view'))
});

gulp.task('css-sourcemap', function() {
    return gulp.src('src/wp-content/themes/vpn-view/style.css')
        .pipe(sourcemaps.init())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('src/wp-content/themes/vpn-view'))
});

gulp.task('css-process-prod', function(callback) {
    runSequence('sass-process', 'css-compress', 'css-prefix', callback);
});

gulp.task('css-process-dev', function(callback) {
    runSequence('sass-process', 'css-prefix', 'css-compress', 'css-sourcemap', callback);
});

gulp.task('watch-dev', function() {
    gulp.watch(sassSources,['css-process-dev']);
});

gulp.task('watch-prod', function() {
    gulp.watch('src/wp-content/themes/vpn-view/scss/main.scss',['css-process-prod']);
});
