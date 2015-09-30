module.exports = {
  css: {
    files: [
      {expand: true, cwd:'<%= pkg.dirs.developement.css %>', src: ['**/*'], dest: '<%= pkg.dirs.public.css %>', filter: 'isFile'}
    ]
  },
  lib: {
    files: [
      {expand: true, cwd:'<%= pkg.dirs.developement.lib %>', src: ['**/*'], dest: '<%= pkg.dirs.public.lib %>', filter: 'isFile'}
    ] 
  } 
}