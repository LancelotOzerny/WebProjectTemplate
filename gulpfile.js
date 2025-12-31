const { parallel, series, watch, src, dest } = require("gulp");
const pug = require('gulp-pug')
const less = require('gulp-less')
const typescript = require('gulp-typescript')
const browserSync = require('browser-sync').create();
const concat = require('gulp-concat');
const imagemin = require('gulp-imagemin');


// ==========================================
// SECTION: Настройка проекта
// ==========================================
const config = {
    inputDir: './src',
    outputDir: './public_html',
}





// ==========================================
// SECTION: Настройка путей проекта
// ==========================================
let pathes = new class {
    pug = {
        follow: config.inputDir + '/**/*.pug',
        src: config.inputDir + '/pages/**/*.pug',
        dest: config.outputDir,
    };

    less = {
        follow: {
            base: config.inputDir + '/less/**/*.less',
            components: config.inputDir + '/components/**/*.less',
        },
        src: {
            base: config.inputDir + '/less/compile/**/*.less',
            components: config.inputDir + '/components/**/*.less',
        },
        dest: config.outputDir + '/assets/css/',
    };

    images = {
        follow: config.inputDir + '/assets/**/*.(png|svg|jpg|jpeg)',
        src: config.inputDir + '/assets/**/*.(png|svg|jpg|jpeg)',
        dest: config.outputDir + '/assets/',
    };

    fonts = {
        follow: config.inputDir + '/assets/fonts/**/*.ttf',
        src: config.inputDir + '/assets/fonts/**/*.ttf',
        dest: config.outputDir + '/assets/fonts/',
    };

    javascript = {
        follow: config.inputDir + '/js/**/*.js',
        src: config.inputDir + '/js/**/*.js',
        dest: config.outputDir + '/assets/scripts/',
    };

    typescript = {
        follow: config.inputDir + '/typescript/**/*.ts',
        src: config.inputDir + '/typescript/**/*.ts',
        dest: config.outputDir + '/assets/scripts/',
    };
};









// ==========================================
// SECTION: Задачи проекта
// ==========================================
// ========================================== BrowserSync
const browserTasker = new class
{
    init()
    {
        browserSync.init({
            server: {
                baseDir: config.outputDir
            },
            files: [
                config.outputDir + '/**/*.html',
                config.outputDir + '/**/*.css',
                config.outputDir + '/**/*.js',
            ],
            notify: false,
            port: 3000,
            open: true,
        });
    }

    reloadPage(done)
    {
        browserSync.reload();
        done();
    }

    updatePage(done)
    {
        browserSync.reload({stream: true});
        done();
    }
}






// ========================================== PUG
const pugTasker = new class
{
    compile()
    {
        return src(pathes.pug.src)
            .pipe(pug({ pretty: true }))
            .pipe(dest(pathes.pug.dest))
    }

    watch()
    {
        watch(pathes.pug.follow, series(this.compile, browserTasker.reloadPage));
    }
}







// ========================================== LESS
const lessTasker = new class
{
    baseCompile()
    {
        return src(pathes.less.src.base)
            .pipe(less({}))
            .pipe(dest(pathes.less.dest))
    }

    componentsCompile()
    {
        return src(pathes.less.src.components)
            .pipe(less({}))
            .pipe(concat('components.css'))
            .pipe(dest(pathes.less.dest))
    }

    watch()
    {
        watch(pathes.less.follow.components, series(this.componentsCompile, browserTasker.updatePage));
        watch(pathes.less.follow.base, series(this.baseCompile, browserTasker.updatePage));
    }
}








// ========================================== TYPESCRIPT
const tsTasker = new class
{
    compile()
    {
        const tsProject = typescript.createProject('tsconfig.json');

        return src(pathes.typescript.src)
            .pipe(tsProject())
            .js
            .pipe(dest(pathes.typescript.dest))
    }

    watch()
    {
        watch(pathes.typescript.follow, series(this.compile, browserTasker.reloadPage));
    }
}










// ========================================== JAVASCRIPT
const jsTasker = new class
{
    optimize()
    {
        return src(pathes.javascript.src)
            .pipe(dest(pathes.typescript.dest))
    }

    watch()
    {
        watch(pathes.javascript.follow, series(this.optimize, browserTasker.reloadPage));
    }
}








// ========================================== IMAGES
const imagesTasker = new class
{
    optimize()
    {
        return src(pathes.images.src, { encoding: false })
            .pipe(imagemin([
                imagemin.gifsicle({interlaced: true}),
                imagemin.mozjpeg({quality: 75, progressive: true}),
                imagemin.optipng({optimizationLevel: 5}),
                imagemin.svgo()
            ]))
            .pipe(dest(pathes.images.dest))
    }

    watch()
    {
        watch(pathes.images.follow, series(this.optimize, browserTasker.reloadPage));
    }
}








// ========================================== FONTS
const fontsTasker = new class
{
    optimize()
    {
        return src(pathes.fonts.src)
            .pipe(dest(pathes.fonts.dest))
    }

    watch()
    {
        watch(pathes.fonts.follow, series(this.optimize, browserTasker.reloadPage));
    }
}









// ==========================================
// SECTION: EXPORTS
// ==========================================

// ========================================== COMPILE
exports.compilePug = pugTasker.compile;
exports.compileLess = series(
    lessTasker.componentsCompile,
    lessTasker.baseCompile,
);
exports.compileTs = tsTasker.compile;

// ========================================== OPTIMIZE
exports.optimizeJs = jsTasker.optimize;
exports.optimizeImages = imagesTasker.optimize;
exports.optimizeFonts = fontsTasker.optimize;


// ========================================== OTHER
exports.default = parallel(
    series(
        pugTasker.compile,
        lessTasker.componentsCompile,
        lessTasker.baseCompile,
        tsTasker.compile,
    ),
    jsTasker.optimize,
    imagesTasker.optimize,
    fontsTasker.optimize,
);

exports.watch = () => {
    browserTasker.init();

    pugTasker.watch();
    lessTasker.watch();
    tsTasker.watch();
    jsTasker.watch();
};