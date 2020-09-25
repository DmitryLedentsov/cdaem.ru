// Стандартный экспорт модуля в nodejs
module.exports = function (grunt) {

    // Инициализация конфига GruntJS
    grunt.initConfig({

        // Less
        less: {
            dev: {
                files: {
                    "../apps/frontend/themes/basic/assets/css_dev/styles.css": "../apps/frontend/themes/basic/assets/less/styles.less",
                    "../apps/frontend/themes/basic/assets/css_dev/partners.css": "../apps/frontend/themes/basic/assets/less/partners.less",
                    "../apps/frontend/themes/basic/assets/css_dev/agency.css": "../apps/frontend/themes/basic/assets/less/agency.less",
                    "../apps/frontend/themes/basic/assets/css_dev/office.css": "../apps/frontend/themes/basic/assets/less/office.less",
                    "../apps/frontend/themes/basic/assets/css_dev/reviews.css": "../apps/frontend/themes/basic/assets/less/reviews.less",
                    "../apps/frontend/themes/basic/assets/css_dev/search.css": "../apps/frontend/themes/basic/assets/less/search.less"
                }
            }
        },

        cssmin: {
            combine: {
                files: {
                    // Общие стили
                    '../apps/frontend/themes/basic/assets/css/styles.css': [
                        '../apps/frontend/themes/basic/assets/css_dev/reset.css',
                        '../apps/frontend/themes/basic/assets/css_dev/styles.css',
                        /*'../apps/frontend/themes/basic/assets/css_dev/ui.css',*/

                        '../apps/frontend/themes/basic/assets/widgets/pnotify/pnotify.custom.min.css',
                        '../apps/frontend/themes/basic/assets/widgets/browsers/jquery.reject.css'
                    ],

                    '../apps/frontend/themes/basic/assets/css/metro-msk.css': [
                        '../apps/frontend/themes/basic/assets/css_dev/metro-msk.css'
                    ],

                    '../apps/frontend/themes/basic/assets/css/office.css': [
                        '../apps/frontend/themes/basic/assets/css_dev/office.css'
                    ],

                    '../apps/frontend/themes/basic/assets/css/search.css': [
                        '../apps/frontend/themes/basic/assets/css_dev/search.css'
                    ]
                }
            }
        },

        uglify: {
            my_target: {
                files: {
                    '../apps/frontend/themes/basic/assets/js/ui.js': [
                        '../apps/frontend/themes/basic/assets/widgets/jquery-autocomplete/jquery.autocomplete.js',
                        '../apps/frontend/themes/basic/assets/widgets/browsers/jquery.reject.min.js',
                        '../apps/frontend/themes/basic/assets/widgets/bootstrap-dropdown/dropdown.js',
                        '../apps/frontend/themes/basic/assets/widgets/bootstrap-tab/tab.js',
                        '../apps/frontend/themes/basic/assets/widgets/bootstrap-modal/modal.js',
                        '../apps/frontend/themes/basic/assets/widgets/bootstrap-select/bootstrap-select.min.js',
                        '../apps/frontend/themes/basic/assets/widgets/pnotify/pnotify.custom.min.js',
                        '../apps/frontend/themes/basic/assets/widgets/scroll/jquery-scrolltofixed-min.js',
                        '../apps/frontend/themes/basic/assets/widgets/inputmask-multi/jquery.maskedinput.min.js',
                        '../apps/frontend/themes/basic/assets/js_dev/formApi.js',
                        '../apps/frontend/themes/basic/assets/js_dev/URI.js',
                        '../apps/frontend/themes/basic/assets/js_dev/ui.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/agency.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/agency.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/callback.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/callback.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/ymap.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/ymap.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/helpdesk.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/helpdesk.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/office.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/office.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/partners.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/partners.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/add-in-slider.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/add-in-slider.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/reviews.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/reviews.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/users.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/users.js'
                    ],

                    '../apps/frontend/themes/basic/assets/js/metro-msk.js': [
                        '../apps/frontend/themes/basic/assets/js_dev/metro-msk.js'
                    ],

                    '../apps/frontend/themes/basic/assets/widgets/bootstrap-date/bootstrap-datetimepicker.min.js': [
                        '../apps/frontend/themes/basic/assets/widgets/bootstrap-date/moment-with-locales.js',
                        '../apps/frontend/themes/basic/assets/widgets/bootstrap-date/bootstrap-datetimepicker.js'
                    ]
                }
            }
        },

        image: {
            static: {
                options: {
                    pngquant: true,
                    optipng: true,
                    advpng: true,
                    zopflipng: true,
                    pngcrush: true,
                    pngout: true,
                    mozjpeg: true,
                    jpegRecompress: true,
                    jpegoptim: true,
                    gifsicle: true,
                    svgo: true
                }/*,
                files: {
                    'dist/img.png': 'src/img.png',
                    'dist/img.jpg': 'src/img.jpg',
                    'dist/img.gif': 'src/img.gif',
                    'dist/img.svg': 'src/img.svg'
                }*/
            },
            dynamic: {
                files: [{
                    expand: true,
                    cwd: '../apps/frontend/web/basic-images/',
                    src: ['**/*.{png,jpg,gif,svg}'],
                    dest: '../apps/frontend/web/basic-images/'
                }]
            }
        },

        watch: {
            scripts: {
                files: [
                    '../apps/frontend/themes/basic/assets/less/*.less'
                    /*'src/less/!*',
                    'src/templates/!*',
                    'src/templates/layouts/!*'*/
                ],
                tasks: [
                    'less',
                    'cssmin' //, 'uglify'
                ],
                options: {
                    interrupt: true
                }
            }
        }
    });

    // Загрузка модулей, которые предварительно установлены
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-image');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Эти задания будут выполнятся сразу же когда вы в консоли напечатание grunt, и нажмете Enter
    grunt.registerTask('default', ['less:dev', 'cssmin', 'uglify', /*'image'*/]);
};