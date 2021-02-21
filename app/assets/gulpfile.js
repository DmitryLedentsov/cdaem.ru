let gulp = require('gulp');
let preprocess = require("gulp-preprocess");

gulp.task('html', function(cb) {
    gulp
        .src('./src/html/*.html')
        .pipe(preprocess({
            context: {

            }
        }))
        .pipe(gulp.dest('./public/'));

    cb();
});


gulp.task('default', gulp.series(['html']));