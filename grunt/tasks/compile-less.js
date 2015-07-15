module.exports = function(grunt) {
  grunt.registerTask('compile-less', ['less', 'csslint', 'copy:css']);
};