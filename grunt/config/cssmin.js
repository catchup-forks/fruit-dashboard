module.exports = {
  // minifies the custom folder css files
  custom: {
    files: {
       '<%= pkg.dirs.developement.css %>/custom.min.css': ['<%= pkg.dirs.developement.css %>/**/*.css']
    }
  }
}