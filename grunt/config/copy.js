module.exports = {
  css: {
    files: [
      {expand: true, cwd:'assets/css', src: ['**/*'], dest: 'public/css', filter: 'isFile'}
      ]
    }  
}