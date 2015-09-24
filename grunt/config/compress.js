module.exports = {
  crx: {
    options: {
      archive: 'external/chrome-extension/dist/dist-<%= grunt.template.today("yyyy-mm-dd") %>.zip'
    },
    files: [
      {expand: true, cwd: 'external/chrome-extension/src', src: ['**/*'], filter: 'isFile'}
    ]
  }
}