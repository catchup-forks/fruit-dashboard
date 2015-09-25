module.exports = {
  css: {
    files: [
      {expand: true, cwd:'assets/css', src: ['**/*'], dest: 'public/css', filter: 'isFile'}
    ]
  },
  lib: {
    files: [
      {expand: true, cwd:'assets/lib', src: ['**/*'], dest: 'public/lib', filter: 'isFile'}
    ] 
  } 
}