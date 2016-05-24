/* global module:false */
module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            coffee: {
                files: [
                    'src/coffeescript/*.coffee',
                    'src/coffeescript/**/*.coffee'
                ],
                tasks: [ 'coffee:compile' ]
            },
            sass: {
                files: [
                    'src/scss/**.scss'
                ],
                tasks: [ 'sass:dist' ]
            }
        },
        sass: {
            dist: {
                options: {
                    trace: true
                },
                files: {
                    'css/default.css' : 'src/scss/default.scss'
                }
            }
        },
        coffee: {
            compile: {
                files: {
                   'js/podcasts.js': [ 'src/coffeescript/*.coffee', 'src/coffeescript/**/*.coffee' ]
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-coffee');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('dev', [ 'coffee', 'sass', 'watch' ]);
    grunt.registerTask('dist', [ 'coffee', 'sass' ]);
};
