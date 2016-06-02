var sass = require("gulp-sass");
var gulp = require("gulp");
var watch = require('gulp-watch');

gulp.task("scss", function () {
    gulp.src("./scss/*.scss")
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest("./css"));
});

gulp.task('watch', ['scss'], function() {
    gulp.watch('./scss/*.scss', ['scss']);
});

gulp.task('default', ['scss'], function() {
    // noop
});
