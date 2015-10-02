'use strict';

module.exports = function(grunt) {
  
  var path = require('path');

  var pkg = grunt.file.readJSON('package.json');

  require('load-grunt-config')(grunt, {
    configPath: path.join(process.cwd(), 'grunt/config'),
    jitGrunt: {
      customTasksDir: 'grunt/tasks'
    },
    data: {
      pkg: pkg // accessible with '<%= pkg. %> from package.json'
    }
  });
};