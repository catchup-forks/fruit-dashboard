module.exports = function(grunt) {
  grunt.registerTask('build-crx', ['clean:crx', 'compress:crx']);
};