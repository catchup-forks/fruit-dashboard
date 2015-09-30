module.exports = {
  developement: {
    options: {
      compress: true,
      optimization: 2,
      banner: '/*!\n' +
              'Application: <%= pkg.name %>\n' +
              'Author: <%= pkg.author.name %>\n' +
              'Author URI: <%= pkg.author.website %>\n' +
              'Description: <%= pkg.description %>\n' +
              '*/' + 
              '\n'
    },
    files: {
      "<%= pkg.dirs.developement.css %>/custom.css": "<%= pkg.dirs.developement.less %>/custom.less" // destination file and source file
    }
  }  
};