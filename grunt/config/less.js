module.exports = {
  developement: {
    options: {
      compress: false,
      yuicompress: true,
      optimization: 2
    },
    files: {
      "assets/css/custom.css": "assets/less/custom.less" // destination file and source file
    }
  }  
};