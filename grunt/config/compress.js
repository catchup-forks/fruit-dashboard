module.exports = {
  crx: {
    options: {
      archive: '<%= pkg.dirs.chrome %>/dist/dist-<%= grunt.template.today("yyyy-mm-dd") %>.zip'
    },
    files: [
      {expand: true, cwd: '<%= pkg.dirs.chrome %>/src', src: ['**/*'], filter: 'isFile'}
    ]
  }
}