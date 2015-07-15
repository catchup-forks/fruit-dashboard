module.exports = {
  // less --> css --> lint --> copy
  process_less: {
    files: ['assets/*/*.less'],
    tasks: ['compile-less'],
    options: {
      nospawn: true
    }
  }
};