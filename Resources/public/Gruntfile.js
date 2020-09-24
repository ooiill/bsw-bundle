module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        less: {
            common: {
                options: {
                    sourceMap: true,
                    sourceMapRootpath: '/public/dist/src-css'
                },
                files: [
                    {
                        expand: true,
                        cwd: 'less/',
                        src: ['**/*.less'],
                        dest: 'dist/src-css/',
                        ext: '.css'
                    }
                ]
            }
        },
        postcss: {
            options: {
                map: {
                    inline: false,
                    prev: 'dist/src-css/'
                },
                processors: [
                    require('autoprefixer')({browsers: 'defaults, last 2 versions, ie >= 9'})
                ]
            },
            common: {
                src: ['!dist/src-css/**/*.keep.css', 'dist/src-css/**/*.css']
            }
        },
        cssmin: {
            common: {
                files: [
                    {
                        expand: true,
                        cwd: 'dist/src-css/',
                        src: ['**/*.css', '!**/*.keep.css'],
                        dest: 'dist/css/',
                        ext: '.css',
                        extDot: 'last'
                    }
                ]
            }
        },
        babel: {
            options: {
                sourceMap: false,
                presets: ['babel-preset-es2015']
            },
            dist: {
                files: [
                    {
                        expand: true,
                        cwd: 'es6/',
                        src: ['**/*.js', '!**/*.keep.js'],
                        dest: 'dist/src-js/',
                        ext: '.js',
                        extDot: 'last'
                    }
                ]
            }
        },
        copy: {
            main: {
                files: [
                    {
                        expand: true,
                        cwd: 'less/',
                        src: ['**/*.keep.css'],
                        dest: 'dist/src-css/'
                    },
                    {
                        expand: true,
                        cwd: 'less/',
                        src: ['**/*.keep.css'],
                        dest: 'dist/css/'
                    },
                    {
                        expand: true,
                        cwd: 'es6/',
                        src: ['**/*.keep.js'],
                        dest: 'dist/src-js/'
                    },
                    {
                        expand: true,
                        cwd: 'es6/',
                        src: ['**/*.keep.js'],
                        dest: 'dist/js/'
                    },
                ],
            },
        },
        uglify: {
            options: {
                mangle: true,
                banner: '/*! <%= pkg.author %> */\n/*! <%= pkg.name %> - <%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                compress: {
                    drop_console: false
                },
                report: 'min'
            },
            common: {
                files: [
                    {
                        expand: true,
                        cwd: 'dist/src-js/',
                        src: ['**/*.js', '!**/*.keep.js'],
                        dest: 'dist/js/',
                        ext: '.js',
                        extDot: 'last'
                    }
                ]
            }
        },
        watch: {
            css: {
                files: ['less/**/*.less'],
                tasks: ['handle-css'],
                options: {
                    spawn: true,
                    interrupt: true
                }
            },
            js: {
                files: ['!es6/**/*.js', 'es6/**/*.js'],
                tasks: ['handle-js'],
                options: {
                    spawn: true,
                    interrupt: true
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-babel');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('listen', ['watch']);
    grunt.registerTask('handle-css', ['less', 'postcss', 'cssmin']);
    grunt.registerTask('handle-js', ['copy', 'babel', 'uglify']);
    grunt.registerTask('handle-all', ['less', 'postcss', 'cssmin', 'copy', 'babel', 'uglify']);
};