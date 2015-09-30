module.exports = {
  // less --> css --> lint --> copy
  process_less: {
    files: ['<%= pkg.dirs.developement.less %>/*.less'],
    tasks: ['compile-less'],
    options: {
      nospawn: true
    }
  },
  copy_lib: {
    files: ['<%= pkg.dirs.developement.lib %>/*'],
    tasks: ['copy:lib'],
    options: {
      nospawn: true
    }
  }
};