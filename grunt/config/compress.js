module.exports = {
  crx: {
    options: {
      archive: 'crx/dist/dist-<%= grunt.template.today("yyyy-mm-dd") %>.zip'
    },
    files: [
      {expand: true, cwd: 'crx/src', src: ['**/*'], filter: 'isFile'}
    ]
  }
}