module.exports = {
  // cleans the crx dist folder
  crx: {
    src: ["<%= pkg.dirs.chrome %>/dist"]
  },
  // cleans the generated files
  generated: {
    // custom css via compile-less
    src: "<%= pkg.dirs.developement.css %>"
  }
}