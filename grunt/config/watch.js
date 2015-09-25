module.exports = {
  // less --> css --> lint --> copy
  process_less: {
    files: ['assets/*/*.less'],
    tasks: ['compile-less'],
    options: {
      nospawn: true
    }
  },
  copy_lib: {
    files: ['assets/lib/*'],
    tasks: ['copy:lib'],
    options: {
      nospawn: true
    }
  }
};