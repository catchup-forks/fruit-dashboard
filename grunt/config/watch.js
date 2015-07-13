module.exports = {
  process_less: {
    files: ['assets/*/*.less'], // which files to watch
    tasks: ['less', 'csslint'],
    options: {
      nospawn: true
    }
  },
  lint_css: {
    files: ['assets/*/*.css'],
    tasks: ['csslint'],
    options: {
      nospawn: true
    }
  }
};