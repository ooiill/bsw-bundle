module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        less: {
            common: {
                files: [{
                    expand: true,
                    cwd: 'dist/',
                    src: ['antd.less'],
                    dest: 'dist/',
                    ext: '.theme.css'
                }]
            }
        },
        postcss: {
            options: {
                processors: [
                    require('autoprefixer')({browsers: 'defaults, last 2 versions, ie >= 9'})
                ]
            },
            common: {
                src: 'dist/antd.theme.css'
            }
        },
        cssmin: {
            common: {
                files: [
                    {
                        expand: true,
                        cwd: 'dist/',
                        src: ['antd.theme.css'],
                        dest: 'dist/',
                        ext: '.min.css',
                        extDot: 'last'
                    }
                ]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('handle-css', ['less', 'postcss', 'cssmin']);
};